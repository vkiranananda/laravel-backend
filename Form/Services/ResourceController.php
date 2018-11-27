<?php

namespace Backend\Root\Form\Services;

use Request;
use App\Http\Controllers\Controller;
use Backend\Root\Core\Services\Helpers;
use Categories;
use App;
use Auth;
use URL;
use Backend\Root\Upload\Services\Uploads;
use \Backend\Root\Upload\Models\MediaFile;
use Content;
use DB;
use GetConfig;

class ResourceController extends Controller {

    use \Backend\Root\Form\Services\Traits\Fields;

    //Имя общего конфиг, если false берется как config
    public $configPath = false;

    //Имя конфига для полей, если false берется как fields
    public $fieldsPath = false;

    //Конфиг общий
    protected $config = [];

    //Конфиг для полей
    protected $fields = [];

    //Переменная где содержатся данные поста 
    protected $post = null;

    //
    // protected $cat = false;

    //Количество страниц в пагинации
    protected $pagination = 30;

    //Шаблон для edit. Берется из конфига.
    private $editTemplate = 'Form::edit';

    //Генерируемый конфиг для веб данных
    protected $configReturn = [];

    //Инитим данные.
    public function init(&$post)
    {

    	$this->post = $post;

    	$baseClass = class_basename($this->post);

    	if (!$this->configPath) $this->configPath = $baseClass."::config";
    	if (!$this->fieldsPath) $this->fieldsPath = $baseClass."::fields";

        $this->config = GetConfig::backend($this->configPath);
        $this->fields = GetConfig::backend($this->fieldsPath);

        $this->config['controllerName'] = '\\'.get_class($this);
        $this->config['baseClass'] = class_basename($this->post);
        $this->config['baseNamespace'] = (preg_match('/(^.+)\\\.+\\\.+/', get_class($this), $mathces)) ? $mathces[1] : '' ;


        if ( isset($this->config['conf']['edit-template']) ) {
            $this->editTemplate = $this->config['conf']['edit-template'];
        }
    }

    //Вввод списка записей
    public function index()
    {
    	$this->resourceCombine('index');
    	//Урл для создания
    	$this->config['create-url'] = action($this->config['controllerName'].'@create');

    	//Добавочные параметры для всех урлов. Устанавливаем значение по умолчанию если нет. 
    	if(!isset($this->config['conf']['url-params'])) {
      		$this->config['conf']['url-params'] = [];
      	}

    	$searchReq = $this->search();
        //Если подключен трейт с категориями
        if( method_exists($this, 'checkCategory') ){
        	$this->config['cat'] = Request::input('cat', false);
      		if( $this->checkCategory($this->config['cat']) ) {
      			//Локализация
	        	$this->localize();
	        	//Параметры для урла
        		$this->config['conf']['url-params'][] = 'cat';
      			
      			if($searchReq) {//Если был произведен поиск, ищем во всех вложенных категориях
      				$this->post = $this->post->whereIn('category_id', Categories::getListIds($this->config['cat'], true));
      			} else {
      				$this->post = $this->post->where('category_id', $this->config['cat']);
      			}
            }
        }

        //Сортировка по умолчанию
        if(isset($this->config['conf']['order-by'])) {
        	foreach ($this->config['conf']['order-by'] as $order) {
	            if(!isset($order['type']) || $order['type'] != 'desc'){
	                $order['type'] = 'asc';
	            }
	            $this->post = $this->post->orderBy($order['col'], $order['type']);
        	}
       	//Сортировка по умолчанию
        } else {
	        $this->post = $this->post->orderBy('id', 'DESC');
	    }	

        //Выставляем title
        $this->config['lang']['title'] = $this->config['lang']['list-title'];

        $templite = (isset($this->config['conf']['list-template'])) ? $this->config['conf']['list-template'] : 'Form::list';



        //Подготавливаем поля
        $this->fields['fields'] = Helpers::changeFieldsOptions($this->fields['fields']);

        //Выставлемя количество записей на странице из конфига
        if(isset($this->config['conf']['list-count-items'])){
        	$this->pagination = $this->config['conf']['list-count-items'];
        }

        return view($templite, [ 'data' => $this->post->paginate($this->pagination), 'config' => $this->config ]);
    }

    //Функция поиска для списка, возвращает true если есть что искать.
    protected function search() {
    	$searchReq = false;
        if(isset($this->config['search'])) {
      	 	foreach ($this->config['search'] as &$field) {
      	 		//Копируем данные поля из основных полей
      	 		if(isset($field['field-from'])){
      	 			$field = array_replace_recursive($this->fields['fields'][$field['field-from']], $field);
      	 		}
      	 		//Добавляем пустой элемент в начало.
      	 		if(isset($field['options-empty'])){
      	 			array_unshift($field['options'], ['value' => '', 'label' => $field['options-empty']]);
      	 		}
      	 		//Создаем выборку
  	 			if(isset($field['name']) && isset($field['fields']) && is_array($field['fields']) ){
 				  	if( ($req = Request::input($field['name'], '')) == '' ) continue;
 					$req = '%'.$req.'%';

 					$searchReq = true;

		  	 		$this->post = $this->post->where(function ($query)
		  	 		use (&$field, $req) 
		  	 		{
		  	 			$first = true;
  	 					foreach ($field['fields'] as $column) {
  	 						if($first) {
  	 							$query = $query->where($column, 'like', $req);
  	 						}else {
  	 							$query = $query->orWhere($column, 'like', $req);
  	 						}
  	 						$first = false;
  	 						
  	 					}
		        	});
	  	 		}
	        }
      	 	// $this->config['search'] = Forms::prepAllFields(false, $this->fields['search']);
    	}
    	return $searchReq;
    }

    //Создаем запись вебка
    public function create()
    {
        $this->resourceCombine('create');
        //For categories if trait enable
        if(method_exists($this, 'setCategoryList')) {
            $this->setCategoryList('create');
            $this->localize();
        }

        $this->configReturn = [ 
        	'config'	=> [
        		'url' 		=> action($this->config['controllerName'].'@store'),
        		'title'		=> $this->config['lang']['create-title'],
        		'method'	=> 'post'
        	], 
        	'fields'	=>	[
        		'fields'	=> $this->prepEditFields( $this->fields['fields'], $this->post ),
        		'tabs'		=> $this->fields['edit']
        	],
        	'data'		=> $this->post, 
        ];

        $this->resourceCombineAfter('create');
   
        return view($this->editTemplate, $this->configResult);
    }

    //Сохраняем запись
    public function store()
    {
        if ( method_exists($this, 'setCategoryList') ) $this->setCategoryList('store');
      	
      	//Вызываем хук
        $this->resourceCombine('store');
      
        //Сохраняем данные в запись
        $relationFields = $this->SaveFields($this->post, $this->fields['fields'], $this->fields['edit']);
        
        //Сохрням юзер id
        if (isset($this->config['conf']['user-id']) ) $this->post->user_id = Auth::user()->id;
        
        $this->post->save();

        if ( method_exists($this, 'saveRelationFields') )$this->saveRelationFields($relationFields);
        
        //Сохраняем медиафайлы
        $this->saveMediaRelations();

        //Вызываем хук
        $this->resourceCombineAfter('store');

        //Обработка редиректов
        if ( isset($this->config['store-redirect-url']) ) {
            return ['redirect' => $this->config['store-redirect-url'] ];
        }

        return $this->edit($this->post['id']);
    }

    //Редактируем запись вебка
    public function edit($id)
    {
    	//Если пост еще не получен, получаем его
        if( !isset($this->post['id']) ) $this->post = $this->post->findOrFail($id);
        
        $this->resourceCombine('edit');
        
        if(method_exists($this, 'setCategoryList')) {
            $this->setCategoryList('edit');
            $this->localize();
        }

        $this->configReturn = [ 
        	'config'	=> [
        		'url' 		=> action($this->config['controllerName'].'@update', $id),
        		'title'		=> $this->config['lang']['edit-title'],
        		'method'	=> 'put',
        		'viewUrl'	=> $this->getViewUrl()
        	], 
        	'fields'	=>	[
        		'fields'	=> $this->prepEditFields( $this->fields['fields'], $this->post ),
        		'tabs'		=> $this->fields['edit']
        	],
        	'data'		=> $this->post, 
        ];

        $this->resourceCombineAfter('edit');
        
        return view($this->editTemplate, $this->configReturn );
    }

    //Обновляем запись
    public function update($id)
    {
        if(!isset($this->post['id'])) $this->post = $this->post->findOrFail( $id );
        $this->resourceCombine('update');
        if(method_exists($this, 'setCategoryList')) $this->setCategoryList('update');
        $relationFields = $this->SaveFields($this->post, $this->fields['fields']);
        $this->post->save();

        if(method_exists($this, 'saveRelationFields'))$this->saveRelationFields($relationFields, true);

        $this->saveMediaRelations();
        $this->resourceCombineAfter('update');

        if(isset($this->config['conf']['update-redirect'])){
        	if(!isset($this->config['conf']['update-redirect-url'])){
        		$this->config['conf']['update-redirect-url'] = action($this->config['controllerName'].'@edit', $this->post->id);
        	}
            return [ 'redirect' => $this->config['conf']['update-redirect-url'] ];
        }

        return [ 
            'viewUrl' => $this->getViewUrl(), 
        ];
    }

    //Показываем запись
    public function show($id)
    {
        if(!isset($this->post['id'])) $this->post = $this->post->findOrFail($id);
        $this->resourceCombine('show');
    
        $this->config['url'] = action($this->config['controllerName'].'@edit', $id);
        $this->config['lang']['title'] = $this->config['lang']['show-title'];
        $template = (isset($this->config['conf']['show-template'])) ? $this->config['conf']['show-template'] : 'Form::show' ;

        //Подготавливаем поля
        $this->fields['fields'] = Helpers::changeFieldsOptions($this->fields['fields']);

        $this->resourceCombineAfter('show');
        
        return view($template,[ 'config' => $this->config, 'data' => $this->post ]  );
    }

    //Удаляем запись
    public function destroy($id)
    {
        $this->post = $this->post->findOrFail( $id );
        
        $this->resourceCombine('destroy');

        if(method_exists($this, 'destroyRelationFields'))$this->destroyRelationFields();

        if(isset($this->config['conf']['media-files']) && $this->config['conf']['media-files'] == 'hidden'){
            Uploads::deleteFiles( MediaFile::where('imageable_type', $this->config['baseClass'])->where('imageable_id', $id)->get() );

            MediaFile::where('imageable_type', $this->config['baseClass'])->where('imageable_id', $id)->delete();
        }
        $this->post->destroy($id);
        $this->resourceCombineAfter('destroy');
    }

    //Сахраняем загруженные данные.
    public function saveMediaRelations($imageable = false, $id = false, $type = 1)
    {
        $mediaFiles = Request::input('_media-file-uploaded-id', false);

        if( is_array($mediaFiles) && count($mediaFiles) > 0 ){
            
            if(isset($this->config['conf']['media-files']) && $this->config['conf']['media-files'] == 'hidden') $type = '2';

            if( $imageable == false ) $imageable = $this->confg['baseClass'];
            if( $id == false ) $id = $this->post->id;

            MediaFile::whereIn('id', $mediaFiles)->where('imageable_type', $imageable)->update(['imageable_id' => $id, 'type' => $type ]);
        }
    }

    // Функция специально  для перегрузки, когда нужно выполнять различне групповые операции перед
    //Сохранием, обновлением, создание или редактированием
    protected function resourceCombine($type){ }

    //Тоже но после сохранения записи. Удобно кэши чистить и прочее..
    protected function resourceCombineAfter($type){ }


    //Функция возвращает урл поста
    protected function getViewUrl()
    {
        if(method_exists($this, 'setCategoryList')) {
            return Content::getUrl($this->post);
        }else {
            return '';
        }
    }
}