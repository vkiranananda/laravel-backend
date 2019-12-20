<?php

namespace Backend\Root\Form\Controllers;

use Request;
use App\Http\Controllers\Controller;
use Helpers;
use Auth;
use \Backend\Root\MediaFile\Models\MediaFileRelation;
use \Backend\Root\MediaFile\Models\MediaFile;
use GetConfig;
use Response;
use Log;

class ResourceController extends Controller {

	use \Backend\Root\Form\Services\Traits\Index;
    use \Backend\Root\Form\Services\Traits\Fields;
    use \Backend\Root\Form\Services\Traits\ListSortable;

    // Имя общего конфига, если false берется как config
    protected $configPath = false;

    // Имя конфига для полей, если false берется как fields
    protected $fieldsPath = false;

    // Конфиг общий
    protected $config = [];

    // Конфиг для полей
    protected $fields = [];

    // Переменная где содержатся данные поста 
    protected $post = null;

    // Генерируемый массив с данными для веб
    protected $dataReturn = [];

    // Модель данных с которой работаем
    public $model = false;

    // Инитим данные.
    function __construct()
    {

    	// Получаем путь до модуля.
    	$baseNamespace = strstr(get_class($this), '\\Controllers', true);
    	// Получаем название модуля
        $moduleName = substr(strrchr($baseNamespace, '\\'), 1);

        // Получаем пути до конфигов
    	if (!$this->configPath) $this->configPath = $moduleName."::config";
    	if (!$this->fieldsPath) $this->fieldsPath = $moduleName."::fields";

    	// Получаем конфиги
        $this->config = array_replace_recursive(
        	GetConfig::backend('Form::config', true),
        	GetConfig::backend($this->configPath)
        );

        $this->fields = GetConfig::backend($this->fieldsPath);

    	// Получаем путь до модуля.
    	$this->config['base-namespace'] = '\\' . $baseNamespace . '\\';
 	  	// Получаем название модуля
        $this->config['module-name'] = $moduleName;
        // Текущий контроллер
  		$this->config['controller-name'] = '\\'.get_class($this);
        // Проверям, если модель установлена в конфиге берем от туда, если нет то по умолчанию.
        $model = (isset($this->config['model'])) ? $this->config['model'] : $this->model;
        
        // Если модель нигде не установлена, пытемся сгенерить сами из имени модуля
        if (!$model) $model = $this->config['base-namespace'].'Models\\'.$moduleName;
        
        $this->post = new $model();

    }

    // Создаем запись вебка
    public function create()
    {
    	$this->resourceCombine('create');

    	// Если стоит опция клонирования получаем запись
        if ( ($clone = Request::input('clone', false)) ) {
        	$this->post = $this->post->findOrFail($clone);
        }

        $this->dataReturn = [ 
        	'config'	=> [
        		'url' 		=> action ($this->config['controller-name'].'@store'),
        		'title'		=> $this->config['lang']['create-title'],
        		'method'	=> 'post',
        		'upload'	=> $this->uploadUrls($clone),
        		'clone-files' => ($this->cloneGetFiles($clone))
        	], 
        	'fields'	=>	[
        		'fields'	=> $this->prepEditFields(),
        		'hidden'	=> $this->prepHiddenFields(),
        		'tabs'		=> $this->fields['edit']
        	],
        ];

        $this->resourceCombineAfter ('create');
   
        return view ($this->config['edit']['template'], $this->dataReturn);
    }



    //Сохраняем запись
    public function store()
    {
      	//Вызываем хук
        $this->resourceCombine('store');

        // Сохраняем данные в запись
        $data = $this->SaveFields();

        // Если ошибка валидации
        if ( $data['errors'] !== true ) return Response::json([ 'errors' => $data['errors'] ], 422);

        // Устанавливаем новое значние поста
        $this->post = $data['post'];

        // Сохрням юзер id
        if ( isset($this->config['user-id']) ) $this->post->user_id = Auth::user()->id;
        
        // Хук перед сохранением
        $this->preSaveData('store');

        $this->post->save();

        // Сохраяняем связи
        if ( method_exists($this, 'saveRelationFields') )$this->saveRelationFields($this->post, $data['relations']);
        
        // Сохраняем медиафайлы
        if ( $this->config['upload']['enable'] ) $this->saveMediaRelations( Request::input('files', []) );

        // Обработка редиректов
        if ( isset($this->config['store-redirect']) ) {
            $this->dataReturn['redirect'] = $this->config['store-redirect'];
        } else {
	        // Выставляем дополнительные параметры.
	        $this->dataReturn = $this->edit($this->post['id']);
	        $this->dataReturn['replaceUrl'] = action($this->config['controller-name'].'@edit', $this->post['id']);
	    }
        
        // Вызываем хук
        $this->resourceCombineAfter('store');

        return $this->dataReturn;
    }

    //Редактируем запись вебка
    public function edit($id)
    {
    	//Если пост еще не получен, получаем его
        if( !isset($this->post['id']) ) $this->post = $this->post->findOrFail($id);
        
        $this->resourceCombine('edit');
        
        $this->dataReturn = [ 
        	'config'	=> [
        		'url' 		=> action($this->config['controller-name'].'@update', $id),
        		'title'		=> $this->config['lang']['edit-title'],
        		'method'	=> 'put',
        		'viewUrl'	=> $this->getViewUrl(),
        		'upload'	=> $this->uploadUrls(),
        		'postId'	=> $this->post['id'],
        	], 
        	'fields'	=>	[
        		'fields'	=> $this->prepEditFields(),
        		'hidden'	=> $this->prepHiddenFields(),
        		'tabs'		=> $this->fields['edit']
        	]
        ];

        $this->resourceCombineAfter('edit');
        
        if ( Request::ajax() ) return $this->dataReturn;

        return view($this->config['edit']['template'], $this->dataReturn );
    }

    //Обновляем запись
    public function update($id)
    {
        if ( !isset($this->post['id']) ) $this->post = $this->post->findOrFail( $id );
        
        $this->resourceCombine('update');
        
    	//Сохраняем данные в запись
        $data = $this->SaveFields();

        //Если ошибка валидации
        if ( $data['errors'] !== true ) return Response::json([ 'errors' => $data['errors'] ], 422);

        // Устанавливаем новое значние поста
        $this->post = $data['post'];

        //Хук перед сохранением
        $this->preSaveData('update');

        $this->post->save();

        //Сохраяняем связи
        if ( method_exists($this, 'saveRelationFields') ) {
        	// Удаялем все записи
        	$this->destroyRelationFields ($this->post);
        	// Добавляем новые
        	$this->saveRelationFields ($this->post, $data['relations']);
        }
        
        //Сохраняем медиафайлы
        if ($this->config['upload']['enable']) $this->saveMediaRelations( Request::input('files', []) );

        //Редиректы
        if ( isset($this->config['update-redirect']) ) {
        	 $this->dataReturn[ 'redirect'] = $this->config['update-redirect'];
        } else {
        	//Выставляем урл просмотра
        	$this->dataReturn = $this->edit($id);
        	// $this->dataReturn['config']['viewUrl'] = $this->getViewUrl();
        }

        //Вызываем хук
        $this->resourceCombineAfter('update');

        return $this->dataReturn;
    }

    //!Показываем запись
    public function show($id)
    {
        if (!isset($this->post['id'])) $this->post = $this->post->findOrFail($id);

        $this->resourceCombine('show');
    
        $this->resourceCombineAfter('show');
        
        return view($this->config['show']['template'] , [ 
        	'config' => $this->config, 
        	'fields' => $this->fields,
        	'data' => $this->post 
        ]);
    }

    //Удаляем запись
    public function destroy($id)
    {
        $this->resourceCombine ('destroy');

        $this->post->destroy ($id);

        $this->resourceCombineAfter ('destroy');

        return $this->dataReturn;
    }

    // Получаем список файлов для клонирования
    protected function cloneGetFiles ($id)
    {
    	$list = ($id) ? MediaFile
			::join('media_file_relations as rel', 'rel.file_id', '=', 'media_files.id')
        	->where('rel.post_id', '=', $id)
        	->where('rel.post_type', '=', class_basename($this->post))
        	->select('media_files.id')
      		->get() : [];

      	$res = [];

      	foreach ($list as $file) $res[] = $file['id'];

      	return $res;
    }

    // Сахраняем загруженные данные.
    public function saveMediaRelations($files, $imageable = false, $id = false)
    {
        if ( is_array($files) && count($files) > 0 ) {

        	// Возможность задать класс для сохранения файла
            if ( $imageable == false ) $imageable = class_basename($this->post);
            // Возможность задать id
            if ( $id == false ) $id = $this->post->id;

            foreach ($files as $fileId) {
            	// Проверит есть ли запись firstOrCreate
            	MediaFileRelation::firstOrCreate([
	        		'file_id' => $fileId,
	        		'post_id' => $id,
	        		'post_type' => $imageable,
	        	]);
            }
        }
    }

    // Получаем url для загрузки, $clone для включения клонирования в урл
    private function uploadUrls ($clone = false) 
    {
    	if ( $this->config['upload']['enable'] ) {

    		$urlPostfix = ($clone == true) ? "?clone=" . $clone : '';
    		
    		return [
    			'uploadUrl' => action($this->config['base-namespace'].'Controllers\\'.$this->config['upload']['controller'].'@index', $this->post['id']).$urlPostfix,
    			'editUrl' => action($this->config['base-namespace'].'Controllers\\'.$this->config['upload']['controller'].'@edit')
    		];
    	} else return false;
    }
    // Функция специально  для перегрузки, когда нужно выполнять различне групповые операции перед
    //Сохранием, обновлением, создание или редактированием
    protected function resourceCombine($type) { }

    //Тоже но после сохранения записи. Удобно кэши чистить и прочее..
    protected function resourceCombineAfter($type) { }

    // Вызывается перед сохранением данных. Что бы была возможность поменять что то в модели, после всех обработок. В параметре type указывается, store update
    protected function preSaveData($type) { }

    //Функция возвращает урл поста
    protected function getViewUrl()
    {
        return '';
    }
}