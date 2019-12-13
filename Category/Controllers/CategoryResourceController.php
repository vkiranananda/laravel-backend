<?php

namespace Backend\Root\Category\Controllers;

use Request;
use Helpers;
use Categories;

// ResourceController для работы с категориями
class CategoryResourceController extends \Backend\Root\Form\Controllers\ResourceController
{
	// parent_cat.
	protected $categoryParentCat = false;

    public function create()
    {
    	// Выставляем значение из параметра.
        $this->post['category_id'] = Request::input('__parent_category_id', 
        	Request::input('cat', false)
    	);
        return parent::create();
    }

    // Добавляем параметр с корневой категорией
    public function index()
    {
    	// Добавляем параметр к урлу
    	$this->config['url-params'][] = 'cat';
        return parent::index();
    }

    // Резолвим имя категории
    protected function indexCategoryField($post, $field, $urlPostfix)
    {
    	$cat = Categories::getCat($post['category_id']);
    	if ($cat) return $cat['name'];
    }

    // Обрабатываем ссылки в списке
    protected function indexLinks($post, $urlPostfix) {
    	$res = parent::indexLinks($post, $urlPostfix);
    	$res['category'] = action($this->config['controller-name'].'@index')
    		. Helpers::mergeUrlParams($urlPostfix, 'parent_cat', $post['category_id']);

    	return $res;
    }

    // Добавляем фильтр по категориям в cортировку
    protected function listSortable()
    {
	    $catID = Request::input('__parent_category_id', false);
	    $this->categoryCheck($catID);
	    $this->post = $this->post->where('category_id', $catID);

        return parent::listSortable();
    }

    // Добавляем кнопку перехода в категории
    protected function indexListMenu($urlPostfix = '')
    {

    	$menu = parent::indexListMenu($urlPostfix);

    	if (($catButton = $this->categoryButton($urlPostfix)))
			array_splice( $menu, 1, 0, [$catButton]);

		return $menu;
    }

    // Кнопка перехода в категорию
    protected function categoryButton($urlPostfix) 
    {
    	$cat = Categories::getCat(Request::input('cat', false));
    	if ($cat['conf']['type'] != 'hierarchical' ) return '';
		return [
			'label' => 'Категории',
			'url' => action('\Backend\Category\Controllers\CategoryController@index').$urlPostfix,
			'btn-type' => 'success',
		];
    }

    // Добавляем префикс у урл
    protected function listSortableButton($urlPostfix) 
    {
    	// Добавляем фильтрацию по категории
    	if ($this->categoryParentCat) {
    		$urlPostfix = Helpers::mergeUrlParams($urlPostfix, '__parent_category_id', $this->categoryParentCat);
    	}

    	return parent::listSortableButton($urlPostfix);
    }

    // Добавляем префикс у урл
    protected function indexListMenuCreateButton($urlPostfix)
	{
		if ($this->categoryParentCat) {
			$urlPostfix = Helpers::mergeUrlParams($urlPostfix, '__parent_category_id', $this->categoryParentCat);
		}

		return parent::indexListMenuCreateButton($urlPostfix);
	}

    // Выводим хлебные крошки.
    protected function indexBreadcrumbs($urlPostfix = '')
    {
    	// Если фильруется по категории.
    	if ($this->categoryParentCat) {
    		$res = [];
    		$url = action($this->config['controller-name'].'@index');
    		// Перебираем все категории
    		$catID = $this->categoryParentCat;
    		do {
    			$cat = Categories::getCat($catID);
    			if (!$cat) abort(403, 'indexBreadcrumbs: категория parent_cat не найдена');

    			$currentUrl = ($this->categoryParentCat == $catID) 
    				? false 
    				: $url . Helpers::mergeUrlParams($urlPostfix, 'parent_cat', $catID);

    			array_unshift($res, [ 'url' => $currentUrl, 'label' => $cat['name']]);

    			$catID = $cat['category_id'];

    		} while($catID != 0);

    		return $res;
    	}

    	return false;
    }


    // Если был произведен поиск, ищем во всех категориях
    protected function indexSearch() {

    	$res = parent::indexSearch();

		$catID = Request::input('cat', false);

		// Если запроса на поиск не было либо в запросе не учавстовал индекс category_id, 
		// Делаем выборку всех записей
		if ( !isset($res['category_id']) ) {
			// category-filter == true выборка данных в текущей категории 
			if (isset($this->config['list']['category-filter']) && $this->config['list']['category-filter'] ){
				$this->post = $this->post->where('category_id', $catID);
				$this->categorySortableUrlCat = $catID;
			// Выборка во всех вложенных категориях
			} else {	
				$this->post = $this->post->whereIn('category_id', Categories::getListIds($catID, true));
			}
			// Запрещаем сортировку, так как выведены все категории раздела
			$this->config['list']['sortable'] = false;
		} else {
			$this->categoryParentCat = $res['category_id'];
		}

    	return $res;
    }

    // Проверочки на корректность данных
    protected function categoryCheck($catID = false)
    {
        // Проверки на валидность данных
    	if ($catID === false) abort(403, 'CategoryResourceController: Категория не указана');

    	$cat = Categories::getCat($catID);
        // Существование категории
        if ($cat === false) {
        	abort(403, 'CategoryResourceController: Категории "'.$catID.'" не существует');
        }
        
        // Эта проверка тоже должна как то быть венесенеа 
        if ($this->config['module-name'] != 'Category') { 
            if ($this->config['module-name'] != $cat['mod'])
                abort(403, 'CategoryResourceController: Модуль категории не соответсвует модулю данных');
        }
    }

    // Получает айдишник текущей категории записи, по типу операции
    protected function categoryGetId($type)
    {
    	if ($type == 'index' || $type == 'create') return Request::input('cat', false);
    	
    	if ($type == 'store' || $type == 'update') {
    		return (isset(Request::input('hidden', [])['category_id'])) 
            	? Request::input('hidden')['category_id']
            	: Request::input('fields')['category_id'];
        }
        
        if ($type == 'edit') return $this->post['category_id'];

        return false;
    }

    protected function resourceCombine($type)
    {
    	if ( array_search($type, ['create', 'edit', 'index', 'store', 'update']) !== false ) {
    		// Получаем айди категории.
    		$catID = $this->categoryGetId($type);
    		// Проверки на валидность данных
        	$this->categoryCheck($catID);
    		// Добавляем дерево категорий в поле
        	$this->categorySetFieldTree($catID);

        	// Локализация
        	if (array_search($type, ['create', 'edit', 'index']) !== false ) {  
        		// Локализуем из модуля категории если нет своей локализации
        		if (!isset( $this->config['lang'])) {
        			$this->config['lang'] = Categories::getRootCat($catID)['lang'];
        		}
        	}

    	}
    }

    //Функция возвращает урл поста
    protected function getViewUrl()
    {
        return Categories::getUrl($this->post);
    }

    // Получаем дерево категорий в массиве для select
    protected function categoryGetFieldTree($rootCat, $exclude = '')
    {
        $tree = Categories::getHtmlTree([ 
            'root' => $rootCat['id'], 
            'offset' => '- ',
            'exclude' => [ $exclude ],
            'first-offset' => true,
        ]);

        return array_merge( [[ 'value' => $rootCat['id'], 'label' => $rootCat['name'] ]], $tree );
    }

    // Получаем дерево категорий и присваиваем его полю
    protected function categorySetFieldTree($catID, $exclude = '')
    {
        // Получаем корневую категорию (раздел)
        $rootCat = Categories::getRootCat($catID);

        // Если не древовидная структура, удалем поле
        if ($rootCat['conf']['type'] != 'hierarchical')	unset($this->fields['fields']['category_id']);

        // Если поле категории не установлено добавляем его в скрытые поля и завершаем
        if (!isset($this->fields['fields']['category_id'])) {
	        $this->fields['hidden'][] = [ 'name' => 'category_id' ];
	        return;
        }

        $this->fields['fields']['category_id']['options'] = $this->categoryGetFieldTree($rootCat);
    }
}