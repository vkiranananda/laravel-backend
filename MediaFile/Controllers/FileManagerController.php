<?php

namespace Backend\Root\MediaFile\Controllers;

use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Services\Uploads;
use Illuminate\Http\Request;
use Auth;
use Log;
use Option;
use Backend\Root\User\Services\UserAccess;

class FileManagerController
{
	protected $config = [
		// Разрешенные форматы файлов, если пустой то разрешены все
		'allow-formats' => [],
		// Диск, по умолчанию filemanager
		'disk' => 'filemanager',
		// Корневой каталог. Если пустой то берем с корня
		'path' => '',
	];

	public function __construct()
	{
		// Инитим локаль
		setlocale(LC_ALL, 'ru_RU.utf8');
	}

	public function index(Request $request)
	{
		return view('MediaFile::file-manager');
	}

	// Получаем список всех файлов в папке, parentId = false - берем с корня
	public function list(Request $request)
	{
		$files = [];

		$parentTree = $this->_getParentTree($request->input('parentId', 0));
		$parentId = $this->_getParentId($parentTree);

		$rootFolder = count($parentTree) == 0;
		$rootShowAll = false;

		// Если папка не корневая и нет доступа, то выходим
		if (!$rootFolder) {
			if (!$this->checkUserAccess('read', $parentTree)) {
				abort(403, 'Нет доступа к просмотру файлов');
			}
		} else {
			// Если папка корневая, то проверяем доступ к папке, правило сработает
			// Только если выбрана опция "все папки"
			if ($this->checkUserAccess('read', $parentTree)) {
				$rootShowAll = true;
			}
		}

		// Проверяем права на запись
		$writeAccess = $this->checkUserAccess('write', $parentTree);

		foreach (MediaFile::where('parent_id', $parentId)
				// На всякий случай проверяем что файлы находятся на нужном диске
				->where('disk', $this->config['disk'])
				->get() as $file) {
			// Если папка корневая, то проверяем доступ к папке
			// Если выбрана опция "все папки" то пропускаем проверку
			if ($rootFolder && !$rootShowAll) {
				$parentTreeCurrent = [$file];
				// Если файл не папка и доступа к корню нет пропускаем
				// Если файл папка и доступа к папке нет пропускаем
				if ($file->type != 'folder' || !$this->checkUserAccess('read', $parentTreeCurrent)) {
					continue;
				}
			}
			$files[] = Uploads::getFileToList($file);
		}

		$parentTreeRes = [];

		// Добавляем родителей в дерево
		foreach ($parentTree as $el) {
			$parentTreeRes[] = Uploads::getFileToList($el);
		}

		// writeAccess
		$result = [
			'urls' => [],
			'parentTree' => $parentTreeRes,
			'files' => $files,
		];

		if ($writeAccess) {
			$result['urls']['upload'] = action('\\' . get_class($this) . '@store', $parentId);
			$result['urls']['createFolder'] = action('\\' . get_class($this) . '@createFolder');
			$result['urls']['delete'] = action('\\' . get_class($this) . '@destroy');
			$result['urls']['move'] = action('\\' . get_class($this) . '@move');
			$result['urls']['copy'] = action('\\' . get_class($this) . '@copy');
		}

		if (Auth::user()->user_role_id == 0) {
			$result['urls']['settings'] = route('file-manager.settings');
		}

		return $result;
	}

	// Загружаем файл, parentId = false - грузим в корень
	public function store(Request $request, $parentId = 0)
	{
		$parentTree = $this->_getParentTree($request->input('parentId', 0));

		if (!$this->checkUserAccess('write', $parentTree)) {
			abort(403, 'Нет доступа к загрузке файлов');
		}

		$file = $request->file('file');
		$path = $this->_getPath($parentTree);
		$name = $request->input('name', $file->getClientOriginalName());

		// Проверяем имя файла и существование файла
		$this->_checkName($path, $name, $this->config['disk']);

		// Сохраняем файл
		$savedFile = Uploads::saveFile($file, [
			'disk' => $this->config['disk'],
			'path' => $path,
			'parentId' => $this->_getParentId($parentTree),
			'name' => $name,
			'orig_name' => $name,
		]);

		return Uploads::getFileToList($savedFile);
	}

	// Создаем папку
	public function createFolder(Request $request)
	{
		$name = $request->input('name', '');
		if (empty($name)) {
			abort(422, 'Не задано название папки');
		}

		// Получаем дерево родителей
		$parentTree = $this->_getParentTree($request->input('parentId', 0));
		$parentId = $this->_getParentId($parentTree);

		if (!$this->checkUserAccess('write', $parentTree)) {
			abort(403, 'Нет доступа к созданию папки');
		}

		$path = $this->_getPath($parentTree);

		$this->_checkName($path, $name, $this->config['disk']);

		$savedFolder = Uploads::createFolder([
			'disk' => $this->config['disk'],
			'path' => $path,
			'parentId' => $parentId,
			'name' => $request->input('name', ''),
		]);

		return Uploads::getFileToList($savedFolder);
	}

	// Перемещаем или переименовываем файл
	public function move(Request $request)
	{
		// --------- Получаем файл исходный файл или папку ---------
		$file = MediaFile::where('key', $request->input('id', 0))
			->where('disk', $this->config['disk'])
			->first();
		if (!$file) {
			abort(404, 'Файл не найден');
		}

		// Проверяем права на исходный файл или папку
		$fileTree = $this->_getParentTree($file->parent_id, false);
		if (!$this->checkUserAccess('write', $fileTree)) {
			abort(403, 'Нет доступа к перемещению файла');
		}

		// --------- Получаем папку назначения ---------
		$toId = $request->input('toId', false);

		// Переименование файла
		if ($toId === false) {
			// Если не задана папка назначения, то переименование файла
			$toPath = $file->path;
			$toParentId = $file->parent_id;
		} else {
			if ($toId == 0) {
				$toFile = ['id' => 0];
				$toTree = [];
				$toPath = Uploads::pathNormalize($this->config['path']);
				$toParentId = 0;
			} else {
				// Получаем папку назначения
				$toFile = MediaFile::where('key', $toId)
					->where('disk', $this->config['disk'])
					->where('type', 'folder')
					->first();
				if (!$toFile) {
					abort(404, 'Папка назначения не найдена');
				}
				$toTree = $this->_getParentTree($toFile['id'], false);
				// Добавляем папку назначения в дерево, чтобы не фыполнять лишние запросы к БД
				$toTree[] = $toFile;
				// Получаем путь к папке назначения
				$toPath = $this->_getPath($toTree);
				$toParentId = $toFile['id'];
			}
			// Если папка назначения не та же, то проверяем права на перемещение файла
			// Что бы не делаит лишние запросы к БД
			if ($toFile['id'] !== $file->parent_id) {
				if (!$this->checkUserAccess('write', $toTree)) {
					abort(403, 'Нет доступа к перемещению файла');
				}
			}
		}

		$toName = $request->input('name', $file->name);

		$movedFile = Uploads::move($file, $toPath, $toName, $toParentId);
		return Uploads::getFileToList($movedFile);
	}

	// Копируем файл или каталог
	public function copy(Request $request)
	{
		// --------- Получаем файл исходный файл или папку ---------
		$file = MediaFile::where('key', $request->input('id', 0))
			->where('disk', $this->config['disk'])
			->first();
		if (!$file) {
			abort(404, 'Файл не найден');
		}

		// Проверяем права на исходный файл или папку
		$fileTree = $this->_getParentTree($file->parent_id, false);

		// Если исходный файл папка, то добавляем его в дерево
		if ($file->type === 'folder') {
			$fileTree[] = $file;
		}

		// Проверяем права на исходный файл или папку
		if (!$this->checkUserAccess('read', $fileTree)) {
			abort(403, 'Нет доступа к копированию файла');
		}

		// --------- Получаем папку назначения ---------
		$toId = $request->input('toId', false);

		if ($toId === false) {
			abort(400, 'Не задана папка назначения');
		}

		// Копируем в корневую папку
		if ($toId == 0) {
			$toFile = ['id' => 0];
			$toTree = [];
		} else {
			// Получаем папку назначения
			$toFile = MediaFile::where('key', $toId)
				->where('disk', $this->config['disk'])
				->where('type', 'folder')
				->first();
			if (!$toFile) {
				abort(404, 'Папка назначения не найдена');
			}

			$toTree = $this->_getParentTree($toFile->parent_id, false);
			$toTree[] = $toFile;
		}

		if (!$this->checkUserAccess('write', $toTree)) {
			abort(403, 'Нет доступа к копированию файла');
		}

		$toName = $file->name;
		$toPath = $this->_getPath($toTree);
		$toParentId = $toFile['id'];

		// Если папка назначения та же, то добавляем постфикс копии
		if ($toFile['id'] === $file->parent_id) {
			if ($file->type !== 'folder') {
				$pName = Uploads::parceFileName($toName);
				$toName = $pName['filename'] . ' - копия' . Uploads::getFileExt($file->extension);
			} else {
				$toName .= ' - копия';
			}
		} else {
			$checkFile = Uploads::getFile($toPath, $toName, $this->config['disk']);
			if ($checkFile) {
				abort(400, ($checkFile->type == 'folder' ? 'Папка' : 'Файл') . ' с таким именем уже существует');
			}
		}

		// Uploads::copy($file, $toPath, $toName, $toParentId);
		$copiedFile = Uploads::copy($file, $toPath, $toName, $toParentId);
		return Uploads::getFileToList($copiedFile);
	}

	// Удаляем файл
	public function destroy(Request $request)
	{
		$file = MediaFile::where('key', $request->input('id', 0))->first();
		if (!$file) {
			abort(404, 'Файл не найден');
		}

		$parentTree = $this->_getParentTree($file->parent_id, false);
		if (!$this->checkUserAccess('write', $parentTree)) {
			abort(403, 'Нет доступа к удалению файла');
		}

		return Uploads::deleteFile($file);
	}

	// Проверяем имя файла или папки
	protected function _checkName($path, $name, $disk)
	{
		$name = Uploads::fileNameNormalize($name);
		if (empty($name)) {
			abort(422, 'Не корректное имя');
		}

		if (($file = Uploads::getFile($path, $name, $disk))) {
			abort(422, $file->type == 'folder' ? 'Папка с таким именем уже существует' : 'Файл с таким именем уже существует');
		}
	}

	// Проверка прав пользователя
	// $access может быть read и write
	protected function checkUserAccess($accessKey, &$parentTree)
	{
		$access = Option::get('_media_file_settings', ['enable-check-access' => 0]);
		// Если не включена проверка прав, то пропускаем
		if ($access['enable-check-access'] != 1) {
			return true;
		}

		$userId = Auth::user()->id;
		$roleId = Auth::user()->user_role_id;

		// Если админы то пропускаем везде.
		if ($roleId == 0)
			return true;

		foreach ($access['policy'] as $policy) {
			// Если выбрана папка, то проверяем ее.
			// Это правило пропускает если выбрано "все папки"
			if ($policy['folder'] != 0) {
				// Если каталог корневой. Значит правило точно к нему не относится
				// Так как выбран каталог.
				if (count($parentTree) == 0) {
					Log::info('root folder');
					continue;
				}
				// Тут смотрим если папка не соответсвует идем к следующему правилу
				elseif ($parentTree[0]['id'] != $policy['folder']) {
					Log::info('folder not match');
					continue;
				}
			}

			// Отсеиваем все записи с permission = read если мы хотим записать
			if ($accessKey == 'write' && $policy['permission'] == 'read') {
				Log::info('read permission');
				continue;
			}

			// Если текущий пользователь не соответствует правилу, то пропускаем
			if ($policy['type'] == 'user') {
				if ($policy['user'] != 0 && $policy['user'] != $userId) {
					Log::info('user');
					continue;
				}
			} else if ($policy['type'] == 'role') {
				// Если роль пользователя не соответствует правилу, то пропускаем
				if ($policy['role'] != $roleId) {
					Log::info('role');
					continue;
				}
			}

			// Если дошли до этого места, то все проверки пройдены и мы можем продолжать
			return true;
		}
		return false;
	}

	// Получаем дерево родителей, key true первый поиск будет по ключу, false по id
	protected function _getParentTree($parentId, $key = true)
	{
		$tree = [];
		$parent = $key ? MediaFile::where('key', $parentId) : MediaFile::where('id', $parentId);
		while ($parentId != 0) {
			$parent = $parent->where('disk', $this->config['disk'])->where('type', 'folder')->first(['id', 'parent_id', 'name', 'path', 'created_at', 'key']);
			// Если родитель не найден, то выходим
			if (!$parent) {
				abort(404, 'Не корректный родитель');
			}
			$tree[] = $parent;
			$parentId = $parent->parent_id;
			$parent = MediaFile::where('id', $parentId);
		}

		return array_reverse($tree);
	}

	// Получаем id родителя из parentTree
	protected function _getParentId($parentTree)
	{
		return (count($parentTree) > 0) ? end($parentTree)->id : 0;
	}

	// Получаем путь к папке
	protected function _getPath(&$parentTree)
	{
		if (count($parentTree) == 0) {
			return Uploads::pathNormalize($this->config['path']);
		}
		$lastEl = end($parentTree);
		return $lastEl->path . $lastEl->name . '/';
	}
}
