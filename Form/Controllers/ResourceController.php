<?php

namespace Backend\Root\Form\Controllers;

use Request;
use App\Http\Controllers\Controller;
use Backend\Root\Core\Services\Helpers;
use Categories;
use App;
use Auth;
use URL;
use Backend\Root\MediaFile\Services\Uploads;
use \Backend\Root\MediaFile\Models\MediaFile;
use Content;
use DB;
use GetConfig;
use Response;
use Log;

class ResourceController extends Controller {

    use \Backend\Root\Form\Services\Traits\Fields;

    //Имя общего конфига, если false берется как config
    protected $configPath = false;

    //Имя конфига для полей, если false берется как fields
    protected $fieldsPath = false;

    //Конфиг общий
    protected $config = [];

    //Конфиг для полей
    protected $fields = [];

    //Переменная где содержатся данные поста 
    protected $post = null;

    //Генерируемый массив с данными для веб
    protected $dataReturn = [];

    //Инитим данные.
    public function init(&$post)
    {

    	$this->post = $post;

    	$baseClass = class_basename($this->post);

    	if (!$this->configPath) $this->configPath = $baseClass."::config";
    	if (!$this->fieldsPath) $this->fieldsPath = $baseClass."::fields";

        $this->config = array_replace_recursive(
        	GetConfig::backend('Form::config'),
        	GetConfig::backend($this->configPath)
        );

        $this->fields = GetConfig::backend($this->fieldsPath);

        $this->config['controllerName'] = '\\'.get_class($this);
        $this->config['baseClass'] = class_basename($this->post);
        $this->config['baseNamespace'] = (preg_match('/(^.+)\\\.+\\\.+/', get_class($this), $mathces)) ? '\\'.$mathces[1] : '' ;

    }

    //!Вввод списка записей
    public function index()
    {
    	$this->resourceCombine('index');

    	//Добавочные параметры для всех урлов. Устанавливаем значение по умолчанию если нет. 
    	if ( !isset($this->config['url-params']) ) $this->config['url-params'] = [];

        //Поиск
    	$searchReq = $this->indexSearch();

        //Сортировка
        $this->indexOrder();

        //Если подключен трейт с категориями
        if ( method_exists($this, 'checkCategory') ) {
     
        	$this->config['cat'] = Request::input('cat', false);
     
      		// Проверяем категорию
      		if ( $this->checkCategory($this->config['cat']) ) {
      			//Локализация
	        	$this->localize();
	        	
	        	// Добавляем параметры для урла
        		$this->config['url-params'][] = 'cat';
      			
      			//Если был произведен поиск, ищем во всех вложенных категориях
      			if ($searchReq) {
      				$this->post = $this->post->whereIn(
      					'category_id', 
      					Categories::getListIds($this->config['cat'], 
      					true
      				));
      			} else {
      				$this->post = $this->post->where('category_id', $this->config['cat']);
      			}
            }
        }

    	//Параметры к урлу
		$urlPostfix = "";

		// Получаем все дополнительные параметры.
		foreach ($this->config['url-params'] as $param) {
			$urlPostfix .= ($urlPostfix == '') ? '?' : '&' ;
			$urlPostfix .= $param.'='.Request::input($param, '');
		}

		$this->dataReturn['config']['urlPostfix'] = $urlPostfix;


        //------------------------------Кнопка Создать----------------------------------------

    	$menu = [];
    	
    	// Если нужно создавать запись
    	if ($this->config['list']['create']) {
			//Создать
			$menu[0]['label'] = isset($this->config['lang']['create-title']) ? $this->config['lang']['create-title'] : 'Создать';
			$menu[0]['link'] = action($this->config['controllerName'].'@create').$urlPostfix;
		}

		//Для ручной сортировки
		if ( isset($this->config['list']['sortable']) ) {
			$menu[] = [
				'label' => 'Сортировка',
				'link'	=> isset($this->config['list']['url-sortable']) ? $this->config['list']['url-sortable'] : action($this->config['controllerName'].'@listSortable').$urlPostfix,
				'type'	=> 'sortable'
			];
		}

		$this->dataReturn['config']['menu'] = $menu;

        //-------------------------------Подготавливаем поля----------------------------------
     
        $fields = [];
        $optionFields = []; // Поля имеющие option
     
     	// Меню для элемента списка
        $this->dataReturn['itemMenu'] = $this->indexItemMenu();

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
        $query = $this->post->paginate($this->config['list']['count-items'])->toArray();
        if ($query['last_page'] < $query['current_page']) {
        	$query = $this->post->paginate($this->config['list']['count-items'], ['*'], 'page', $query['last_page'])->toArray();
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
        	
        	$res['_links'] = $this->indexLinks($post);

        	foreach ($fields as $field) {
        		
        		$name = $field['name'];

        		if (isset($field['func'])) {
        			$func = $field['func'];
        			$res[$name] = $this->$func($post, $field);
        			continue;
        		}

        		//Выставляем значние
        		$value = Helpers::dataIsSetValue($post, $name, '');

        		if ( isset($optionFields[$name][$value]) ) $res[$name] = $optionFields[$name][$value];
        		else $res[$name] = $value;
        	}
        	$this->dataReturn['items']['data'][] = $res;
        }

        //Получаем шаблон 
    	$templite = (isset($this->config['list']['template'])) ? $this->config['list']['template'] : 'Form::list';

    	//Хук
    	$this->resourceCombineAfter ('index');

        if ( Request::ajax() ) return $this->dataReturn;

        return view($templite, [ 'data' => $this->dataReturn ]);
    }
    
    // Получаем пункты меню для конкртеной строки списка
    protected function indexItemMenu() {
    	if (isset($this->config['list']['item-menu'])) {
    		$res = [];
    		foreach ($this->config['list']['item-menu'] as $item) {
    			// Если есть опция default, то берем значения из дефолтного меню
    			if (isset($item['default'])) {
    				$res[] = $this->config['list']['item-menu-default'][$item['default']];
    			}
    			else $res[] = $item;
    		}
    		return $res;
    	}
    	return $this->config['list']['item-menu-default'];
    }

    // Обрабатываем ссылки в списке
    protected function indexLinks($post) {
    	return [
    		'edit' 		=> action($this->config['controllerName'].'@edit', $post['id']),
    		'destroy' 	=> action($this->config['controllerName'].'@destroy', $post['id'])
    	];
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
      	 		// Тип выборки, по умолчанию like
      	 		$typeComparison = (isset($field['type-comparison'])) ? $field['type-comparison'] : 'like';

      	 		//Копируем данные поля из основных полей
      	 		if ( isset($field['field-from']) ) {
      	 			$field = array_replace_recursive($this->fields['fields'][$field['field-from']], $field);
      	 		}
      	 		
      	 		//Добавляем пустой элемент в начало.
      	 		if ( isset($field['options-empty'] ) ){
      	 			array_unshift($field['options'], ['value' => '', 'label' => $field['options-empty']]);
      	 		}
      	 		
      	 		//Создаем выборку
  	 			if (isset($field['name']) && isset($field['fields']) && is_array($field['fields']) ){
 				  	
 				  	$field['value'] = Request::input($field['name'], '');
 				  	
 				  	if ( $field['value'] == '' ) continue;

 				  	// По умолчанию добавляем %% для запроса
 				  	$req = (isset($field['exact-match'])) ? $field['value'] : '%'.$field['value'].'%';

 					$searchReq = true;

 					//Выборка по группе полей, если в каком то поле есть то данные выедутся
		  	 		$this->post = $this->post->where(function ($query)
		  	 		use (&$field, $req, $typeComparison) 
		  	 		{
		  	 			$first = true;
  	 					foreach ($field['fields'] as $column) {
  	 						if ($first) $query = $query->where($column, $typeComparison, $req);
  	 						else $query = $query->orWhere($column, $typeComparison, $req);

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
        		'fields'	=> $this->prepEditFields(),
        		'hidden'	=> $this->prepHiddenFields(),
        		'tabs'		=> $this->fields['edit']
        	]
        ];

        $this->resourceCombineAfter ('create');
   
        return view ($this->config['edit']['template'], $this->dataReturn);
    }

    //Сохраняем запись
    public function store()
    {
      	//Вызываем хук
        $this->resourceCombine('store');

    	//Обработка категорий
    	if ( method_exists($this, 'setCategoryList') ) $this->setCategoryList('store');
      
        //Сохраняем данные в запись
        $data = $this->SaveFields();

        //Если ошибка валидации
        if ( $data['errors'] !== true ) return Response::json([ 'errors' => $data['errors'] ], 422);

        // Устанавливаем новое значние поста
        $this->post = $data['post'];

        //Сохрням юзер id
        if ( isset($this->config['user-id']) ) $this->post->user_id = Auth::user()->id;
        
        //Хук перед сохранением
        $this->preSaveData('store');

        $this->post->save();

        //Сохраяняем связи
        if ( method_exists($this, 'saveRelationFields') )$this->saveRelationFields($data['relations']);
        
        //Сохраняем медиафайлы
        if ( $this->config['upload']['enable'] ) $this->saveMediaRelations( Request::input('files', []) );

        //Обработка редиректов
        if ( isset($this->config['store-redirect']) ) {
            $this->dataReturn['redirect'] = $this->config['store-redirect'];
        } else {
	        //Выставляем дополнительные параметры.
	        $this->dataReturn = $this->edit($this->post['id']);
	        $this->dataReturn['replaceUrl'] = action($this->config['controllerName'].'@edit', $this->post['id']);
	    }
        
        //Вызываем хук
        $this->resourceCombineAfter('store');

        return $this->dataReturn;
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
        
        //Обработка категорий
        if ( method_exists($this, 'setCategoryList') ) $this->setCategoryList('update');
        
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
        	//Удаялем все записи
        	$this->destroyRelationFields ($this->post);
        	//Добавляем новые
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
    	if ( $this->config['upload']['enable'] )
    		return [
    			'uploadUrl' => action($this->config['baseNamespace'].'\\Controllers\\'.$this->config['upload']['controller'].'@index', $this->post['id']),
    			'editUrl' => action($this->config['baseNamespace'].'\\Controllers\\'.$this->config['upload']['controller'].'@edit')
    		];
    	else return false;
    }
    // Функция специально  для перегрузки, когда нужно выполнять различне групповые операции перед
    //Сохранием, обновлением, создание или редактированием
    protected function resourceCombine($type){ }

    //Тоже но после сохранения записи. Удобно кэши чистить и прочее..
    protected function resourceCombineAfter($type){ }

    // Вызывается перед сохранением данных. Что бы была возможность поменять что то в модели, после всех обработок. В параметре type указывается, store update
    protected function preSaveData($type){ }


    //Функция возвращает урл поста
    protected function getViewUrl()
    {
        if (method_exists($this, 'setCategoryList')) return Content::getUrl($this->post);
        else return '';
    }
}