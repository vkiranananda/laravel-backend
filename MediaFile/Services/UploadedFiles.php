<?php
namespace Backend\Root\MediaFile\Services;

use Content;
use Helpers;
use Log;
use \Backend\Root\MediaFile\Models\MediaFile;
use \Backend\Root\MediaFile\Models\MediaFileRelation;
use \Backend\Root\MediaFile\Services\Uploads;

class UploadedFiles
{
	// Массив с изображениями
	private $files = [];
	// Запрошенные файлы в текущем запросе
	private $reqFiles = [];
	// Тип возвращаемого значения.
	private $reqResultArray = true;
	// Прелоадинг файлов
	private $loadFiles = [];

	// Генерим миниатюрку к файлу пример: [100, 100, 'fit'], [100, 'auto'] и возвращает массив с урлами 
	// или оригинальный файл если $original = true
	public function thumbUrl($size, $original = false)
	{
		// Добавляем ключи в массив для загрузки
		$this->loadByKeys($this->reqFiles);
		// Получаем файлы из базы данных
		$this->getFiles();

		$res = [];

		foreach ($this->reqFiles as $key) {
			$resUrl = [
				'orig' => '',
				'thumb' => '',
			];

			// Если файла нет игнорим
			if (isset($this->files[$key])) {
				$file = &$this->files[$key];
				$resUrl['orig'] = Uploads::getUrl($file);
				// Если файл изображение, то генерируем миниатюру
				if (count($size) > 0 && $file['type'] == 'image') {
					// Если gif, то возвращаем оригинал
					if ($file['extension'] == 'gif') {
						$resUrl['thumb'] = Uploads::getUrl($file);
					} else {
						$resThumb = Uploads::getThumbnail($file, $size);
						$resUrl['thumb'] = Uploads::getBaseUrl($resThumb['key'] . '.jpg');
					}
				}
			}

			// Если запрошен оригинал, то возвращаем массив
			if ($original) {
				$res[] = $resUrl;
			} else {
				$res[] = $resUrl['thumb'];
			}

			// Выводим первый элемент если запрошен только один элемент
			if ($this->reqResultArray === false) {
				return $res[0];
			}
		}

		if ($this->reqResultArray === false) {
			return ($original) ? ['orig' => '', 'thumb' => ''] : '';
		}

		return $res;
	}

	// Получаем все ранее иниченные файлы и сохраняем в массив картинок
	// Данная функция нужна что бы получить все файлы из базы данных одним запросом.
	public function getFiles()
	{
		// Формируем новый массив на выборку, если файла нет в общем массиве
		$keysReq = [];

		foreach ($this->loadFiles as $key) {
			// Проверяем загружен ли файл из базы данных
			if (!isset($this->files[$key])) {
				// Если файла нет в массиве, то добавляем его в массив на выборку
				$keysReq[$key] = $key;
			}
		}
		// Если есть файлы для выборки, то выбираем их из базы данных
		if (count($keysReq) > 0) {
			foreach (MediaFile::whereIn('key', $keysReq)->get() as $img) {
				$this->files[$img['key']] = $img;
			}
		}

		$this->loadFiles = [];
	}

	// Получаем все картинки в списке записей оптом, что бы не плодить запросы
	// Например нужно для списка элементов. Если $first == true, выбираем только первые картинки
	public function loadByList($list, $field, $first = true)
	{
		$reqKeys = [];
		foreach ($list as $post) {
			$keys = $this->getKeys(Helpers::getDataField($post, $field, []));
			if (count($keys) > 0) {
				// Только первые картинки
				if ($first)
					$reqKeys[] = reset($keys);
				else
					$reqKeys = $keys;
			}
		}
		$this->loadByKeys($reqKeys);
	}

	// Добавляет данные для автоматической загрузки всех изображений, что бы не плодить запросы
	// указывается массив ключей изображений
	public function loadByKeys($keys)
	{
		if (is_array($keys)) {
			foreach ($keys as $key) {
				$this->loadFiles[$key] = $key;
			}
		}

		return $this;
	}

	// Добавляет данные для автоматической загрузки всех изображений, что бы не плодить запросы
	// Можно указать как название поля, так и массив из названий поля
	public function loadByField($post, $field)
	{
		$fields = (!is_array($field)) ? [$field] : $field;

		foreach ($fields as $field) {
			$this->loadByKeys($this->getKeys(Helpers::getDataField($post, $field)));
		}

		return $this;
	}

	// Добавляет данные для автоматической загрузки всех изображений, что бы не плодить запросы
	// указывается модель данных. Запрашиваются все файлы пренадлежашие этой модели.
	// public function loadByPost($post)
	// {
	// 	$className = class_basename($post);
	// 	if (isset($post['id']) && $className != '') {
	// 		foreach (MediaFile::join('media_file_relations as rel', 'rel.file_id', '=', 'media_files.id')
	// 				->where('rel.post_id', '=', $post->id)
	// 				->where('rel.post_type', '=', $className)
	// 				->orderBy('id', 'desc')
	// 				->get() as $img) {
	// 			$this->images[$img['id']] = $img;
	// 		}
	// 	}
	// 	return $this;
	// }


	// app('UploadedFiles')->getByField($post, 'gallery')->size([128, 128, 'fit'])->htmlImg(['class' => 'thumb']);
	// Получаем массивы картинок из поля.
	// app('UploadedFiles')->get($post['images'])->files();

	// Получаем данные по ключам. Можно указать как массив так и единичный элемент
	// Результат будет таким же либо массив либо единичный элемент
	public function get($keys, $first = false)
	{
		$this->reqResultArray = ($first) ? false : true;

		if (is_array($keys)) {  // Если массив
			if (count($keys) > 0) {
				if ($first)
					$this->reqFiles = [reset($keys)];
				else
					$this->reqFiles = $keys;
			} else
				$this->reqFiles = [];
		} else {  // Иначе
			// Если значение не установлено ничего не возвращаем
			if ($keys == '' || $keys == false)
				$this->reqFiles = [];
			else
				$this->reqFiles = [$keys];
		}

		return $this;
	}

	// Инитим данные из поля, для выборки массива
	public function getByField($post, $field, $first = false)
	{
		$keys = $this->getKeys(Helpers::getDataField($post, $field, []));

		// Устанавливаем тип возвращаемого значения
		$this->reqResultArray = ($first) ? false : true;
		
		if (empty($keys))
			return $this;

		$this->get($keys, $first);

		return $this;
	}

	// Получить список урлов
	public function url()
	{
		// Добавляем ключи в массив для загрузки
		$this->loadByKeys($this->reqFiles);
		// Получаем файлы из базы данных
		$this->getFiles();

		$res = [];

		foreach ($this->reqFiles as $key) {
			// Если файла нет игнорим
			if (!isset($this->files[$key]))
				continue;

			$res[] = Uploads::getUrl($this->files[$key]);

			if (!$this->reqResultArray)
				return $res[0];
		}

		return $res;
	}

		// Получить список урлов
	public function files()
	{
		// Добавляем ключи в массив для загрузки
		$this->loadByKeys($this->reqFiles);
		// Получаем файлы из базы данных
		$this->getFiles();

		$res = [];

		foreach ($this->reqFiles as $key) {
			// Если файла нет игнорим
			if (!isset($this->files[$key]))
				continue;

			$res[] = Uploads::getFileData($this->files[$key]);

			if (!$this->reqResultArray)
				return $res[0];
		}

		return $res;
	}

	// Выведет нужный ключ или значение второго параметра defValue.
	// public function keyOrEmpty($key, $defValue = '', $attr = [])
	// {
	// 	$res = $this->url($attr);

	// 	return Helpers::getDataField($res, $key, $defValue);
	// }

	// Получить массив файлов
	// public function files()
	// {
	// 	$this->loadByArray($this->reqFiles);
	// 	$this->getFiles();

	// 	$res = [];

	// 	foreach ($this->reqFiles as $id) {
	// 		// Если файла нет игнорим
	// 		if (!isset($this->images[$id]))
	// 			continue;

	// 		$res[] = &$this->images[$id];
	// 	}

	// 	if ($this->reqResultArray)
	// 		return $res;
	// 	elseif (count($res) > 0)
	// 		return $res[0];

	// 	return [];
	// }

	// Просто удаляем файл. Если связи есть, файл не трогается. Нужно удалить сначала связи.
	// Если передан массив, удалит массив файлов.
	// private function deleteFiles($files)
	// {
	// 	if (!is_array($files))
	// 		$files = [$files];

	// 	if (count($files) == 0)
	// 		return;

	// 	// Меням ключи со значениями местами
	// 	$delFiles = array_flip($files);

	// 	// Смотрим, если для какой то записи есть связь, не удаляем файл, убирая его из массива.
	// 	foreach (MediaFileRelation::whereIn('file_id', $files)->get(['file_id']) as $rel) {
	// 		if (isset($delFiles[$rel['file_id']]))
	// 			unset($delFiles[$rel['file_id']]);
	// 	}

	// 	// Закидываем ключи обратно в массив
	// 	$delFiles = array_keys($delFiles);

	// 	// Если файлов на удаление больше 0, то удаляем.
	// 	if (count($delFiles) > 0)
	// 		Uploads::deleteFiles(MediaFile::whereIn('id', $delFiles)->get());
	// }

	// Удаляет  файл, с переданными связями postType и postId,
	// если передан массив, удалит массив файлов.
	// если останутся еще какие то связи то файл не удалится.
	// Если передан четвертый параметр как true. То удалятся только связи, а файл остенется.
	// public function deleteFilesByRelation($files, $postType, $postId, $soft = false)
	// {
	// 	if (!is_array($files))
	// 		$files = [$files];

	// 	if (count($files) == 0)
	// 		return;

	// 	// Удаляем связи
	// 	$relations = MediaFileRelation::whereIn('file_id', $files)
	// 		->where('post_type', $postType)
	// 		->where('post_id', $postId)
	// 		->delete();

	// 	// Если удаление мягкое, то файлы не трогаем.
	// 	if ($soft)
	// 		return;

	// 	// Иначе пытаемся грохнуть файлы физически
	// 	$this->deleteFiles($files);
	// }

	// Получаем ключи из массива данных
	private function getKeys($data)
	{
		if (!is_array($data))
			return [];

		// Если единичный элемент, то возвращаем его ключ
		if (isset($data['id']))
			return [$data['id']];

		// Получаем ключи из массива данных
		return array_map(function ($item) {
			return $item['id'];
		}, $data);
	}
}
