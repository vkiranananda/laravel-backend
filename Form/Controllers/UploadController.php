<?php

namespace Backend\Root\Form\Controllers;

use Backend\Root\Core\Services\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Backend\Root\Form\Models\MediaFile;
use Backend\Root\Form\Services\Uploads;
use Content;
use GetConfig;
//type 0 deleted, 1 good, 2 hidden(for only field)

class UploadController extends Controller
{
	use \Backend\Root\Form\Services\Traits\Fields;

	//Название модуля
	protected $moduleName = '';
	//Если установить в false будет загружен базовый конфиг
    protected $configPath = false;

    // Если малоли хочется изменить поля в форме редактирования файлв.
    protected $editConfigPath = 'Form::upload-edit';

    public function __construct()
    {
    	//Инитим локаль
    	setlocale(LC_ALL, 'ru_RU.utf8');

       	if ($this->moduleName == '') abort(403, 'UploadController: moduleName не установлена');
    }

    //Получаем список всех файлов для записи
    public function index($id = false)
    {
    	$thisClass = '\\'.get_class($this);

        $list = ($id) ? MediaFile::where('imageable_type', $this->moduleName)->where('imageable_id', $id)->orderBy('id', 'desc')->get() : [];

        return [
        	'urls' => [
        		'upload' => action($thisClass.'@store'),
        		'destroy' => action($thisClass.'@destroy', ''),
        	],
        	'files' => app('UploadedFiles')->prepGaleryData($list)
        ];
    }

    //Загружаем файл
    public function store(Request $request)
    {
    	// Получаем конфиг
    	$config = ($this->configPath === false)
    		? $this->config = GetConfig::backend("Form::upload")
	       	: $this->config = array_replace_recursive (
	       		GetConfig::backend("Form::upload"),
	       		GetConfig::backend($this->configPath)
	       	);

        $this->validate( $request, [ 'file' => $config['validate'] ] );
        $config['module'] = $this->moduleName;

        $savedFile[] = Uploads::saveFile($config);

        return app('UploadedFiles')->prepGaleryData( $savedFile )[0];
    }

    public function destroy($id)
    {
        Uploads::deleteFiles( [ MediaFile::findOrFail($id) ] );
    }

	//Получаем данные о картинке
    public function edit($id)
    {
    	$file = MediaFile::where('imageable_type', $this->moduleName)->findOrFail($id);

    	$size = ($file['file_type'] == 'image') ? $file['sizes']['orig']['size'][0].' x '.$file['sizes']['orig']['size'][1] : '';

    	return [
    		'origSize' => $size,
    		'date' => date( 'd.m.Y', strtotime( $file['created_at'] ) ),
    		'fields' => $this->_getFields($file),
    		'saveUrl' => action('\\'.get_class($this).'@update' , $file->id)
    	];
    }

    //Получаем поля
    private function _getFields(&$file)
    {
    	$fields = GetConfig::backend($this->editConfigPath);

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
        foreach (GetConfig::backend($this->editConfigPath) as $name => $field) {
        	if ( ($value = $request->input($name, false)) ) {
        		$arrayData['fields'][$name] = $value;
        	} 
        }
        
        $file['array_data'] = $arrayData;

        $file->save();
    }



}
