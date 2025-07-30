<?php

namespace Backend\Root\MediaFile\Controllers;

use Backend\Root\Core\Services\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Backend\Root\MediaFile\Models\MediaFile;
use Backend\Root\MediaFile\Models\MediaFileRelation;
use Backend\Root\MediaFile\Services\Uploads;
use Content;
use GetConfig;
use UploadedFiles;
use Storage;

use Log;

class UploadController extends Controller
{
	// use \Backend\Root\Form\Services\Traits\Fields;

	//Название модуля
	protected $moduleName = '';
	//Если установить в false будет загружен базовый конфиг
    protected $configPath = false;

    // Если малоли хочется изменить поля в форме редактирования файлв.
    protected $editConfigPath = false;

    public function __construct()
    {
    	//Инитим локаль
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

    //Загружаем файл
    public function store(Request $request)
    {
    	// Получаем конфиг
    	// $config = ($this->configPath === false)
    	// 	? $this->config = GetConfig::backend("MediaFile::upload", true)
	    //    	: $this->config = array_replace_recursive (
	    //    		GetConfig::backend("MediaFile::upload", true),
	    //    		GetConfig::backend($this->configPath)
	    //    	);

      //   $this->validate( $request, [ 'file' => $config['validate'] ] );
      //   $config['module'] = $this->moduleName;

      //   $savedFile[] = Uploads::saveFile($config);

      //   return UploadedFiles::prepGaleryData( $savedFile )[0];
    }
}
