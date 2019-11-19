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
        	GetConfig::backend('Form::config'),
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

    //!Вввод списка записей
    public function index()
    {
    	$this->resourceCombine('index');

        //Поиск
    	$this->indexSearch();
        //Сортировка
        $this->indexOrder();

    	//Параметры к урлу
		$urlPostfix = "";

		// Получаем все дополнительные параметры.
		foreach ($this->config['url-params'] as $param) {
			$urlPostfix = Helpers::mergeUrlParams($urlPostfix, $param, Request::input($param, ''));
		}

		$this->dataReturn['config']['urlPostfix'] = $urlPostfix;

        //------------------------------Кнопка Создать----------------------------------------

		$this->dataReturn['config']['menu'] = $this->indexListMenu($urlPostfix);

		// -----------------------------Хлебные крошки----------------------------------------

		$this->dataReturn['breadcrumbs'] = $this->indexBreadcrumbs($urlPostfix);

        //-------------------------------Подготавливаем поля----------------------------------
     
        $fields = [];
        $fields_prep = []; // Методы доп обработки values
        $optionFields = []; // Поля имеющие option
     
     	// Меню для элемента списка
        $this->dataReturn['itemMenu'] = $this->indexItemMenu();


        foreach ($this->fields['list'] as $field) {
        
        	// Получаем базовое поле. ВСЕ ПОЛЯ ДОЛЖНЫ БЫТЬ КОРНЕВЫМИ
        	$mainField = ( isset ($this->fields['fields'][ $field['name'] ]) ) ? $this->fields['fields'][ $field['name'] ] : [];

        	//Выставляем метку если на задано
        	if ( !isset($field['label']) && isset($mainField['label']) ) {
        		$field['label'] = $mainField['label'];
        	}

        	$fields [] = $field;
        	$fields_prep [] = $this->initField(array_replace($mainField, $field));
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

    	// Добавляем поля поиска
    	if ( isset($this->fields['search']) ) {
    		foreach ($this->fields['search'] as $field) {
    			unset($field['fields']); // удаляем ненужные опции
    			$this->dataReturn['search'][] = $field;
    		}
    	}

        // Подготваливаем все поля
        foreach ($query['data'] as $post) {
        	$res = []; //Преобразованные данные
        	
        	$res['_links'] = $this->indexLinks($post);

        	foreach ($fields as $key => $field) {
        		
        		$name = $field['name'];

        		if (isset($field['func'])) {
        			$func = $field['func'];
        			$res[$name] = $this->$func($post, $field);
        			continue;
        		}

        		// Обработчик полей
        		$res[$name] = 
        			$fields_prep[$key]->list( Helpers::getDataField($post, $name, '') );
        	}
        	$this->dataReturn['items']['data'][] = $res;
        }

        //Получаем шаблон 
    	$templite = (isset($this->config['list']['template'])) ? $this->config['list']['template'] : 'Form::list';

    	//Хук перед выходом
    	$this->resourceCombineAfter ('index');

        if ( Request::ajax() ) return $this->dataReturn;

        return view($templite, [ 'data' => $this->dataReturn ]);
    }
    
    // Выводим хлебные крошки.
    protected function indexBreadcrumbs($url_postfix = '')
    {
    	return false;
    }

    // Главное меню в списке. url_postfix добавочная строка у url адресу.
    protected function indexListMenu($url_postfix = '')
    {

    	$menu = [];
    	
    	// Если нужно создавать запись
    	if ($this->config['list']['create']) {
    		$menu[] = $this->indexListMenuCreateButton($url_postfix);
		}

		// Для ручной сортировки
		if ( isset($this->config['list']['sortable']) && $this->config['list']['sortable']) {
			$menu[] = $this->listSortableButton($url_postfix);
		}

		return $menu;
    }


    // Кнопка создать
	protected function indexListMenuCreateButton($url_postfix)
	{
		return [
			'label' => isset($this->config['lang']['create-title']) 
				? $this->config['lang']['create-title'] : 'Создать',
			'url' => action($this->config['controller-name'].'@create').$url_postfix,
			'btn-type' => 'primary'
		];
	}

    // Получаем пункты меню для строки списка
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
    		'edit' 		=> action($this->config['controller-name'].'@edit', $post['id']),
    		'destroy' 	=> action($this->config['controller-name'].'@destroy', $post['id'])
    	];
    }


    // Функция для сортировки списка
    protected function indexOrder() 
    {
    	$order = Request::input('order', false);

        // Если выставлена опция ручной сортировки, то сортировка по умолчанию будет по sort_num
        if ( isset($this->config['list']['sortable']) && $this->config['list']['sortable'] )  {
        	$orderField = 'sort_num';
        	$orderType = 'asc'; //от меньшего к большему
        } else {
        	$orderField = $this->config['list']['default-order']['col'];
        	$orderType = $this->config['list']['default-order']['type'];
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
      	 	foreach ($this->fields['search']  as $key => &$field) {
      	 		//Проверяем на валидность
  	 			if (!isset($field['name']) || !isset($field['fields']) || !is_array($field['fields']) ) continue;
 				  	

      	 		//Копируем данные поля из основных полей
      	 		if (isset($field['field-from'])) {
      	 			// Если поле не существует, удаляем текущее поле из поиска
      	 			if (!isset($this->fields['fields'][$field['field-from']])) {
      	 				unset($this->fields['search'][$key]);
      	 				continue;
      	 			}

      	 			$field = array_replace_recursive($this->fields['fields'][$field['field-from']], $field);
      	 			unset($field['field-from']);
      	 		}

 				$field['value'] = Request::input($field['name'], '');

      	 		//Добавляем пустой элемент в начало.
      	 		if (isset($field['options-empty']) && isset($field['options']) && is_array($field['options'])){
      	 			array_unshift($field['options'], ['value' => '', 'label' => $field['options-empty']]);
      	 		}

				if ( $field['value'] == '' ) continue;

 				$req = $field['value'];

      	 		// Проверяем значения и добавляем дополнительные опции из options
      	 		if ($field['type'] == 'select') {

      	 			$option = Helpers::searchArray($field['options'], 'value', $field['value']);

      	 			// Если нет значния
      	 			if (!$option) abort(403, 'indexSearch: select value not found '.$field['value']);

	      	 		// подменяем элемент нельзя передать в строке запроса. например null
	      	 		if ( array_key_exists('change-value', $option) ) {
	      	 			$req = $option['change-value'];
	      	 			unset($option['change-value']);
	      	 		}

	      	 		// Получаем нужные опции
      	 			foreach (['type-comparison', 'exact-match'] as $key) {
      	 				if (isset($option[$key])) {
      	 					$field[$key] = $option[$key];
      	 					unset($option[$key]);
      	 				}
      	 			}

      	 			if (!isset($field['exact-match']))$field['exact-match'] = true;
      	 			if (!isset($field['type-comparison']))$field['type-comparison'] = '=';
      	 		}

      	 		// Тип выборки, по умолчанию like
      	 		$typeComparison = 'like';
      	 		if (isset($field['type-comparison'])) {
      	 			$typeComparison = $field['type-comparison'];
      	 			unset($field['type-comparison']);
      	 		}

			  	// По умолчанию добавляем %% для запроса
			  	if (isset($field['exact-match'])) unset($field['exact-match']);
			  	else $req = '%'.$req.'%';

				//Выборка по группе полей, если в каком то поле есть то данные выведутся
		 		$this->post = $this->post->where(function ($query)
		 		use (&$field, $req, $typeComparison, &$searchReq) 
		 		{
					$first = true;

					foreach ($field['fields'] as $column) {
						// Выборка для релатед полей
						$func = ($first) ? 'where' : 'orWhere';
						
						$searchReq[$column] = $req;

						if (isset($field['field-save']) && $field['field-save'] == 'relation'){
							$func .= 'Has';
							$query = $query->$func('relationFields', function ($query) 
							use ($column, $req, $typeComparison, $first) 
							{
								$query->where('value', $typeComparison, $req)->where('field_name', $column);
							});
						} else $query = $query->$func($column, $typeComparison, $req);

						$first = false;
					}
				});
	        }
    	}

    	return $searchReq;
    }

    // Создаем запись вебка
    public function create()
    {
        $this->resourceCombine('create');
     
        $this->dataReturn = [ 
        	'config'	=> [
        		'url' 		=> action ($this->config['controller-name'].'@store'),
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
        if ( method_exists($this, 'saveRelationFields') )$this->saveRelationFields($this->post, $data['relations']);
        
        //Сохраняем медиафайлы
        if ( $this->config['upload']['enable'] ) $this->saveMediaRelations( Request::input('files', []) );

        //Обработка редиректов
        if ( isset($this->config['store-redirect']) ) {
            $this->dataReturn['redirect'] = $this->config['store-redirect'];
        } else {
	        //Выставляем дополнительные параметры.
	        $this->dataReturn = $this->edit($this->post['id']);
	        $this->dataReturn['replaceUrl'] = action($this->config['controller-name'].'@edit', $this->post['id']);
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
        
        $this->dataReturn = [ 
        	'config'	=> [
        		'url' 		=> action($this->config['controller-name'].'@update', $id),
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
            if ( $imageable == false ) $imageable = class_basename($this->post);
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
    			'uploadUrl' => action($this->config['base-namespace'].'Controllers\\'.$this->config['upload']['controller'].'@index', $this->post['id']),
    			'editUrl' => action($this->config['base-namespace'].'Controllers\\'.$this->config['upload']['controller'].'@edit')
    		];
    	else return false;
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