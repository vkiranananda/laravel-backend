<?php

namespace Backend\Root\MediaFile\Controllers;

use App\Http\Controllers\Controller;
use Backend\Root\Core\Services\Helpers;
use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Models\MediaFileRelation;
use Backend\Root\MediaFile\Services\Uploads;
use Illuminate\Http\Request;
use Content;
use GetConfig;
use Log;
use Storage;
use UploadedFiles;

class UploadController extends Controller
{
	// use \Backend\Root\Form\Services\Traits\Fields;

	// Название модуля
	protected $moduleName = '';
	// Если установить в false будет загружен базовый конфиг
	protected $configPath = false;
	// Если малоли хочется изменить поля в форме редактирования файлв.
	protected $editConfigPath = false;

	public function __construct()
	{
		// Инитим локаль
		setlocale(LC_ALL, 'ru_RU.utf8');
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

	// Загружаем файл
	public function store(Request $request)
	{
		$file = $request->file('file');
		$name = $request->input('name', $file->getClientOriginalName());

		// Сохраняем файл
		$savedFile = Uploads::saveFile($file, [
			'orig_name' => $name,
		]);

		$file = Uploads::getFileToList($savedFile);
		$file['name'] = $file['orig_name'];

		return $file;
	}
}
