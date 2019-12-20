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

       	if ($this->moduleName == '') abort(403, 'UploadController: moduleName не установлена');
    }

    // Получаем список всех файлов для записи
    public function index(Request $request, $id = 0)
    {
    	$thisClass = '\\'.get_class($this);

		$list = ($id != 0) ? MediaFile
			::join('media_file_relations as rel', 'rel.file_id', '=', 'media_files.id')
        	->where('rel.post_id', '=', $id)
        	->where('rel.post_type', '=', $this->moduleName)
        	->select('*')
      		->orderBy('id', 'desc')->get() : [];

        return [
        	'urls' => [
        		'upload' => action($thisClass.'@store'),
        		'destroy' => action($thisClass.'@destroy', [$id, '']),
        	],
        	'files' => UploadedFiles::prepGaleryData($list),
        	'clone' => $request->input('clone', false)
        ];
    }

    //Загружаем файл
    public function store(Request $request)
    {
    	// Получаем конфиг
    	$config = ($this->configPath === false)
    		? $this->config = GetConfig::backend("MediaFile::upload", true)
	       	: $this->config = array_replace_recursive (
	       		GetConfig::backend("MediaFile::upload", true),
	       		GetConfig::backend($this->configPath)
	       	);

        $this->validate( $request, [ 'file' => $config['validate'] ] );
        $config['module'] = $this->moduleName;

        $savedFile[] = Uploads::saveFile($config);

        return UploadedFiles::prepGaleryData( $savedFile )[0];
    }

    public function destroy($postId, $fileId)
    {

    	if ($postId != 0) {
    		// Удаляем связь
    		MediaFileRelation::where('file_id', $fileId)
    			->where('post_type', $this->moduleName)
    			->where('post_id', $postId)->delete();
    	}

    	if (MediaFileRelation::where('file_id', $fileId)->count() == 0) {
    		// Удаляем сам файл если нет связей.
    		Uploads::deleteFiles( [ MediaFile::findOrFail($fileId) ] );
    	}
    }

	// Получаем данные о картинке
    public function edit($id)
    {
    	$file = MediaFile::findOrFail($id);

    	$size = ($file['file_type'] == 'image') ? $file['sizes']['orig']['size'][0].' x '.$file['sizes']['orig']['size'][1] : '';

    	return [
    		'origSize' => $size,
    		'date' => date( 'd.m.Y', strtotime( $file['created_at'] ) ),
    		'fields' => $this->_getFields($file),
    		'saveUrl' => action('\\'.get_class($this).'@update' , $file->id)
    	];
    }

    // Получаем поля
    private function _getFields(&$file)
    {
    	
    	$fields = $this->getEditConfig();

    	// Удаляем поля если тип file
    	if($file['file_type'] != 'image'){
    		unset($fields['img_title'], $fields['img_alt']);
    	}
    	
    	// Наполняем поля данными
    	foreach ($fields as $name => &$field) {
    		$field['value'] = ( isset($file['array_data']['fields'][$name]) ) ? $file['array_data']['fields'][$name] : '' ;
    	}

    	return $fields;
    }

    //Изменяем информацию о картинке
    public function update(Request $request, $id)
    {
    	$file = MediaFile::findOrFail($id);

        //Сохраняем данные в запись
        $arrayData = $file['array_data'];

        foreach ($this->getEditConfig() as $name => $field) {
        	if ( ($value = $request->input($name, false)) ) {
        		$arrayData['fields'][$name] = $value;
        	} 
        }
        
        $file['array_data'] = $arrayData;

        $file->save();
    }

    protected function getEditConfig ()
    {
    	return ($this->editConfigPath === false) ? GetConfig::backend("MediaFile::upload-edit", true) 
    		: GetConfig::backend($this->editConfigPath);
    }


}
