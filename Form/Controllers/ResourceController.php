<?php

namespace Backend\Root\Form\Controllers;

use Request;
use App\Http\Controllers\Controller;
use Backend\Root\Core\Services\Helpers;
use Categories;
use App;
use Auth;
use URL;
use Backend\Root\Form\Services\Uploads;
use \Backend\Root\Form\Models\MediaFile;
use Content;
use DB;
use GetConfig;
use Response;
use Log;

class ResourceController extends Controller {

    use \Backend\Root\Form\Services\Traits\Fields;

    //Имя общего конфиг, если false берется как config
    protected $configPath = false;

    //Имя конфига для полей, если false берется как fields
    protected $fieldsPath = false;

    //Конфиг общий
    protected $config = [];

    //Конфиг для полей
    protected $fields = [];

    //Переменная где содержатся данные поста 
    protected $post = null;

    //Поготовленный вывод для index
    // protected $dataReturn = [];

    // protected $cat = false;

    //Количество страниц в пагинации 30
    protected $pagination = 30;

    //Шаблон для edit. Берется из конфига.
    private $editTemplate = 'Form::edit';

    //Контролер для работы с загрузкой файлов, лучше не менять, там много что на это завязано
    private $uploadController = 'UploadController';

    //Генерируемый массив с данными для веб
    protected $dataReturn = [];

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
        $this->config['baseNamespace'] = (preg_match('/(^.+)\\\.+\\\.+/', get_class($this), $mathces)) ? '\\'.$mathces[1] : '' ;

        if ( isset($this->config['edit-template']) ) $this->editTemplate = $this->config['edit-template'];
    }

    //!Вввод списка записей
    public function index()
    {
    	$this->resourceCombine('index');

    	//Добавочные параметры для всех урлов. Устанавливаем значение по умолчанию если нет. 
    	if ( !isset($this->config['url-params']) ) $this->config['url-params'] = [];

        // Выставлемя количество записей на странице из конфига
        if ( isset($this->config['list']['count-items']) ) $this->pagination = $this->config['list']['count-items'];

        //Поиск
    	$searchReq = $this->indexSearch();

        //Сортировка
        $this->indexOrder();

        // //Если подключен трейт с категориями
        // if ( method_exists($this, 'checkCategory') ) {
        // 	$this->config['cat'] = Request::input('cat', false);
      		// if ( $this->checkCategory($this->config['cat']) ) {
      		// 	//Локализация
	       //  	$this->localize();
	       //  	//Параметры для урла
        // 		$this->config['url-params'][] = 'cat';
      			
      		// 	//Если был произведен поиск, ищем во всех вложенных категориях
      		// 	if ($searchReq) {
      		// 		$this->post = $this->post->whereIn('category_id', Categories::getListIds($this->config['cat'], true));
      		// 	} else {
      		// 		$this->post = $this->post->where('category_id', $this->config['cat']);
      		// 	}
        //     }
        // }

    	//Параметры к урлу
		$urlPostfix = "";

		//Получаем все дополнительные параметры.
		foreach ($this->config['url-params'] as $param) {
			$urlPostfix .= ($urlPostfix == '') ? '?' : '&' ;
			$urlPostfix .= $param.'='.Request::input($param, '');
		}

		$this->dataReturn['config']['urlPostfix'] = $urlPostfix;


        //------------------------------Кнопка Создать----------------------------------------

    	$menu = [];

		//Создать
		$menu[0]['label'] = isset($this->config['lang']['create-title']) ? $this->config['lang']['create-title'] : 'Создать';
		$menu[0]['link'] = action($this->config['controllerName'].'@create').$urlPostfix;

		//Для ручной сортировки
		if ( isset($this->config['list']['sortable']) ) {
			$menu[1]['label'] = 'Сортировка';
			$menu[1]['link'] = isset($this->config['list']['url-sortable']) ? $this->config['list']['url-sortable'] : action($this->config['controllerName'].'@listSortable').$urlPostfix;
			$menu[1]['type'] = 'sortable';
		}

		$this->dataReturn['config']['menu'] = $menu;

        //-------------------------------Подготавливаем поля----------------------------------
     
        $fields = [];
        $optionFields = []; //Поля имеющие option
     
        foreach ($this->fields['list'] as $field) {
        
        	//Получаем базовое поле. ВСЕ ПОЛЯ ДОЛЖНЫ БЫТЬ КОРНЕВЫМИ
        	$mainField = ( isset ($this->fields['fields'][ $field['name'] ]) ) ? $this->fields['fields'][ $field['name'] ] : [];

        	//Выставляем имя если на задано
        	if ( !isset($field['label']) && isset($mainField['label']) ) {
        		$field['label'] = $mainField['label'];
        	}

        	//Подготавливаем опции в нужный формат, что бы подставлять верное значение
        	if ( isset( $mainField['options']) && is_array($mainField['options']) ) {
        		$optionFields[ $field['name'] ] = Helpers::optionsToArr($mainField['options']);
        	}

        	$fields [] = $field;

        }

        // Делаем выборку
        $query = $this->post->paginate($this->pagination)->toArray();
        if ($query['last_page'] < $query['current_page']) {
        	$query = $this->post->paginate($this->pagination, ['*'], 'page', $query['last_page'])->toArray();
        }

        // Для пагинации
        $this->dataReturn['items']['currentPage'] = $query['current_page'];
        $this->dataReturn['items']['lastPage'] = $query['last_page'];
        
        //урл страницы списка.
        $this->dataReturn['config']['indexUrl'] = $query['path'];

        //Список полей для вывода
        $this->dataReturn['fields'] = $fields;
        $this->dataReturn['config']['title'] = $this->config['lang']['list-title'];

		$this->dataReturn['items']['data'] = [];

    	//Погдотавливаем поля поиска
    	if ( isset($this->fields['search']) ) {
    		foreach ($this->fields['search'] as $field) {
    			unset($field['fields']); //удаляем ненужные опции
    			$this->dataReturn['search'][] = $field;
    		}
    	}

        //Подготваливаем все поля
        foreach ($query['data'] as $post) {
        	$res = []; //Преобразованные данные
        	
        	foreach ($fields as $field) {
        		
        		$name = $field['name'];

        		//Выставляем значние
        		$value = Helpers::dataIsSetValue($post, $name);

        		if ( isset($optionFields[$name][$value]) ) $res[$name] = $optionFields[$name][$value];
        		else $res[$name] = $value;

				//Обрабатываем ссылки
        		if ( isset($field['link']) ) {
        			if ($field['link'] == 'edit') $res['_links']['edit'] = action($this->config['controllerName'].'@edit', $post['id']);
        		}
        		$res['_links']['destroy'] = action($this->config['controllerName'].'@destroy', $post['id']);
        		// $res['_id']['id'] = 
        		
        	}
        	$this->dataReturn['items']['data'][] = $res;
        }

        //Получаем шаблон 
    	$templite = (isset($this->config['list']['template'])) ? $this->config['list']['template'] : 'Form::list';

    	//Хук
    	$this->resourceCombineAfter ('index');
        
        // dd($this->dataReturn);
        
        if ( Request::ajax() ) return $this->dataReturn;

        return view($templite, [ 'data' => $this->dataReturn ]);
    }

    //Функция для сортировки списка
    protected function indexOrder() 
    {
 		$order = Request::input('order', false);

        //Если выставлена опция ручной сортировки, то сортировка по умолчанию будет по sort_num
        if ( isset($this->config['list']['sortable']) )  {
        	$orderField = 'sort_num';
        	$orderType = 'asc'; //от меньшего к большему
        } else {
        	$orderField = 'id'; //Иначе по id
        	$orderType = 'desc'; //От большего к меньшему
        }

        if ($order !== false && isset($this->fields['list'][$order]['sortable']) ) {
        	$orderType = Request::input('order-type', 'desc');
        	$orderField = $this->fields['list'][$order]['name'];
        	$this->fields['list'][$order]['sortable'] = $orderType;
        }

        $this->post = $this->post->orderBy($orderField, $orderType);
    }

    //Функция поиска для списка, возвращает true если есть что искать.
    protected function indexSearch() {
    	
    	$searchReq = false;
        
        //Если есть поля для поиска
        if ( isset($this->fields['search']) ) {
      	 	//Перебираем
      	 	foreach ($this->fields['search'] as &$field) {
      	 		
      	 		//Копируем данные поля из основных полей
      	 		if ( isset($field['field-from']) ) {
      	 			$field = array_replace_recursive($this->fields['fields'][$field['field-from']], $field);
      	 		}
      	 		
      	 		//Добавляем пустой элемент в начало.
      	 		if ( isset($field['options-empty'] ) ){
      	 			array_unshift($field['options'], ['value' => '', 'label' => $field['options-empty']]);
      	 		}
      	 		
      	 		//Создаем выборку
  	 			if ( isset($field['name']) && isset($field['fields']) && is_array($field['fields']) ) {
 				  	
 				  	$field['value'] = Request::input($field['name'], '');
 				  	
 				  	if ( $field['value'] == '' ) continue;

 					$req = '%'.$field['value'].'%';

 					$searchReq = true;

 					//Выборка по группе полей, если в каком то поле есть то данные выедутся
		  	 		$this->post = $this->post->where(function ($query)
		  	 		use (&$field, $req) 
		  	 		{
		  	 			$first = true;
  	 					foreach ($field['fields'] as $column) {
  	 						if ($first) $query = $query->where($column, 'like', $req);
  	 						else $query = $query->orWhere($column, 'like', $req);

  	 						$first = false;
  	 					}
		        	});
	  	 		}
	        }
    	}
    	return $searchReq;
    }

    //Создаем запись вебка
    public function create()
    {
        $this->resourceCombine('create');
        
        //Если трейт с категориями включен
        if ( method_exists($this, 'setCategoryList') ) {
            $this->setCategoryList('create');
            $this->localize();
        }

        $this->dataReturn = [ 
        	'config'	=> [
        		'url' 		=> action ($this->config['controllerName'].'@store'),
        		'title'		=> $this->config['lang']['create-title'],
        		'method'	=> 'post',
        		'upload'	=> $this->_getUploadUrls()
        	], 
        	'fields'	=>	[
        		'fields'	=> $this->prepEditFields ( $this->fields['fields'], $this->post ),
        		'tabs'		=> $this->fields['edit']
        	]
        ];

        $this->resourceCombineAfter ('create');
   
        return view ($this->editTemplate, $this->dataReturn);
    }

    //Сохраняем запись
    public function store()
    {
        if ( method_exists($this, 'setCategoryList') ) $this->setCategoryList('store');
      	
      	//Вызываем хук
        $this->resourceCombine('store');
      
        //Сохраняем данные в запись
        $data = $this->SaveFields(
        	$this->post, 
        	$this->fields['fields'], 
        	Request::input('fields'), 
        	$this->fields['edit']
        );

        //Если ошибка валидации
        if ( $data['errors'] !== true ) return Response::json([ 'errors' => $data['errors'] ], 422);

        $this->post = $data['post'];
        
        //Сохрням юзер id
        if ( isset($this->config['user-id']) ) $this->post->user_id = Auth::user()->id;
        
        $this->post->save();

        //Сохраяняем связи
        if ( method_exists($this, 'saveRelationFields') )$this->saveRelationFields($data['relations']);
        
        //Сохраняем медиафайлы
        if ( isset($this->config['uploads']) ) $this->saveMediaRelations( Request::input('files', []) );

        //Вызываем хук
        $this->resourceCombineAfter('store');

        //Обработка редиректов
        if ( isset($this->config['store-redirect']) ) {
            return ['redirect' => $this->config['store-redirect'] ];
        }

        //Выставляем дополнительные параметры.
        $editResult = $this->edit($this->post['id']);
        $editResult['replaceUrl'] = action($this->config['controllerName'].'@edit', $this->post['id']);

        return $editResult;
    }

    //Редактируем запись вебка
    public function edit($id)
    {
    	//Если пост еще не получен, получаем его
        if( !isset($this->post['id']) ) $this->post = $this->post->findOrFail($id);
        
        $this->resourceCombine('edit');
        
        //Если трейт с категориями включен
        if ( method_exists($this, 'setCategoryList') ) {
            $this->setCategoryList('edit');
            $this->localize();
        }

        $this->dataReturn = [ 
        	'config'	=> [
        		'url' 		=> action($this->config['controllerName'].'@update', $id),
        		'title'		=> $this->config['lang']['edit-title'],
        		'method'	=> 'put',
        		'viewUrl'	=> $this->getViewUrl(),
        		'upload'	=> $this->_getUploadUrls(),
        		'postId'	=> $this->post['id'],
        	], 
        	'fields'	=>	[
        		'fields'	=> $this->prepEditFields( $this->fields['fields'], $this->post ),
        		'tabs'		=> $this->fields['edit']
        	]
        ];
//        Log::info( print_r($this->post->toArray(), true) );

        $this->resourceCombineAfter('edit');
        
        if ( Request::ajax() ) return $this->dataReturn;

        return view($this->editTemplate, $this->dataReturn );
    }

    //Обновляем запись
    public function update($id)
    {
        if ( !isset($this->post['id']) ) $this->post = $this->post->findOrFail( $id );
        
        $this->resourceCombine('update');
        
        if(method_exists($this, 'setCategoryList')) $this->setCategoryList('update');
        
    	//Сохраняем данные в запись
        $data = $this->SaveFields(
        	$this->post, 
        	$this->fields['fields'], 
        	Request::input('fields', []),
        	$this->fields['edit']);

        //Если ошибка валидации
        if ( $data['errors'] !== true ) return Response::json([ 'errors' => $data['errors'] ], 422);

        $this->post = $data['post'];
        
        $this->post->save();

        //Сохраяняем связи
        if ( method_exists($this, 'saveRelationFields') ) {
        	//Удаялем все записи
        	$this->destroyRelationFields ($this->post);
        	//Добавляем новые
        	$this->saveRelationFields ($this->post, $data['relations']);
        }
        
        //Сохраняем медиафайлы
        if ( isset($this->config['uploads']) ) $this->saveMediaRelations( Request::input('files', []) );

        //Вызываем хук
        $this->resourceCombineAfter('update');

        if ( isset($this->config['update-redirect']) ) {
        	return [ 'redirect' => $this->config['update-redirect'] ];
        }

        return [ 'config' => [ 'viewUrl' => $this->getViewUrl() ] ];
    }

    //!Показываем запись
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
        $this->resourceCombine ('destroy');
        $this->post->destroy ($id);
        $this->resourceCombineAfter ('destroy');

        //Редиректим на индекс
        if (Request::input('_index-redirect', false) ) return $this->index();
    }

    //Сахраняем загруженные данные.
    public function saveMediaRelations($files, $imageable = false, $id = false)
    {
        if ( is_array($files) && count($files) > 0 ) {

        	//Возможность задать класс для сохранения файла
            if ( $imageable == false ) $imageable = $this->config['baseClass'];
            //Возможность задать id
            if ( $id == false ) $id = $this->post->id;

            //Сохраняем.
            MediaFile::whereIn('id', $files)
            	->where('imageable_type', $imageable)
            	->update( ['imageable_id' => $id, 'type' => 2 ] );
        }
    }

    //Получаем url для загрузки
    private function _getUploadUrls ()
    {
    	if ( isset ($this->config['uploads']) && $this->config['uploads'] === true )
    		return [
    			'uploadUrl' => action($this->config['baseNamespace'].'\\Controllers\\'.$this->uploadController.'@index', $this->post['id']),
    			'editUrl' => action($this->config['baseNamespace'].'\\Controllers\\'.$this->uploadController.'@edit')
    		];
    	else return false;
    }
    // Функция специально  для перегрузки, когда нужно выполнять различне групповые операции перед
    //Сохранием, обновлением, создание или редактированием
    protected function resourceCombine($type){ }

    //Тоже но после сохранения записи. Удобно кэши чистить и прочее..
    protected function resourceCombineAfter($type){ }


    //Функция возвращает урл поста
    protected function getViewUrl()
    {
        if (method_exists($this, 'setCategoryList')) return Content::getUrl($this->post);
        else return '';
    }
}