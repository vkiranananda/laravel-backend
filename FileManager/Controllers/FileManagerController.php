<?php

namespace Backend\Root\FileManager\Controllers;

use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Services\Uploads;
use Illuminate\Http\Request;
use GetConfig;
use Storage;
use Log;

class FileManagerController
{
	protected $config;
	// Если установить в false будет загружен базовый конфиг
	// protected $configPath = false;
	// Если малоли хочется изменить поля в форме редактирования файлв.
	// protected $editConfigPath = false;

	public function __construct()
	{
		// Инитим локаль
		setlocale(LC_ALL, 'ru_RU.utf8');

		$this->config = GetConfig::backend('FileManager::upload');
	}

	public function index(Request $request)
	{
		return view('FileManager::index');
	}

	// Получаем файл ?download=true для скачивания

	public function getFile(Request $request, $key)
	{
		// Удаляем расширение из key, если оно есть
		$key = preg_replace('/\.[^.]+$/', '', $key);

		$file = MediaFile::where('key', $key)->first();
		if (!$file) {
			abort(404, 'Файл не найден');
		}

		$filePath = Storage::disk($file->disk)->path($file->path . $file->name);

		if (!file_exists($filePath)) {
			abort(404, 'Файл не найден на диске');
		}

		if ($request->input('download', false)) {
			return response()->download($filePath, $file->name_orig);
		}

		// Добавляем кеширование для файлов из файлового менеджера
		return response()->file($filePath, [
			'Cache-Control' => 'public, max-age=2592000',  // 30 дней
			'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000)
		]);
	}

	// Получаем список всех файлов в папке, parentId = false - берем с корня
	public function list(Request $request)
	{
		$files = [];

		$parentTree = $this->_getParentTree($request->input('parentId', 0));
		$parentId = $this->_getParentId($parentTree);

		foreach (MediaFile::where('parent_id', $parentId)
				// На всякий случай проверяем что файлы находятся на нужном диске
				->where('disk', $this->config['disk'])
				->get() as $file) {
			$files[] = $this->_getFileArray($file);
		}

		$parentTreeRes = [];

		// Добавляем родителей в дерево?
		foreach ($parentTree as $el) {
			$parentTreeRes[] = $this->_getFileArray($el);
		}

		return [
			'urls' => [
				'upload' => action('\\' . get_class($this) . '@store', $parentId),
				'createFolder' => action('\\' . get_class($this) . '@createFolder'),
				'delete' => action('\\' . get_class($this) . '@destroy'),
				'move' => action('\\' . get_class($this) . '@move'),
				'copy' => action('\\' . get_class($this) . '@copy'),
			],
			'parentTree' => $parentTreeRes,
			'files' => $files,
		];
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

		return $this->_getFileArray($savedFile);
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

		return $this->_getFileArray($savedFolder);
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

		return $this->_getFileArray(Uploads::move($file, $toPath, $toName, $toParentId));
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
		return $this->_getFileArray(Uploads::copy($file, $toPath, $toName, $toParentId));
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
	protected function checkUserAccess($access, &$parentTree)
	{
		return true;
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

	// Получаем массив данных о файле для отображения в списке
	private function _getFileArray($file)
	{
		$dateConfig = GetConfig::backend('backend');
		$res = [
			'id' => $file->key,
			'name' => $file->name,
			'orig_name' => $file->orig_name,
			'type' => $file->type,
			'size' => $file->size,
			'timestamp' => $file->created_at->timestamp,
			'dateFormatted' => (new \Carbon\Carbon($file->created_at))
				->setTimezone($dateConfig['time-zone'])
				->format($dateConfig['datetime-format']),
		];

		if ($file->type !== 'folder') {
			$res['url'] = $this->_getUrl($file);
		}

		$thumbnail = Uploads::getThumbnail($file, ['80', '80', 'fit']);

		if ($thumbnail) {
			$res['thumb'] = $this->_getUrl($thumbnail);
		}

		return $res;
	}

	private function _getUrl($fileData)
	{
		return route('file-manager.get-file', $fileData['key'] . Uploads::getFileExt($fileData['extension']));
	}
}
