<?php

namespace Backend\Root\Category\Controllers;

use Request;
use Categories;
use Helpers;

class CategoryController extends \Backend\Root\Category\Controllers\CategoryResourceController
{
    public function store()
    {
    	// Выставлеям модуль из корневой категории
        $this->post['mod'] = Categories::getRootCat(Request::input('fields', [])['category_id'])['mod'];

        return parent::store();
    }

    protected function resourceCombine($type)
    {
        parent::resourceCombine($type);

        if (array_search($type, ['create', 'edit', 'index']) !== false ) {  
        	$rootCat = Categories::getRootCat($this->categoryGetId($type));
        	// Добавляем информацию о разделе
        	if ($type == 'index') {
        		$this->config['lang']['list-title'] = $rootCat['name'] . " - " . $this->config['lang']['list-title'];
        	} else {
        		$this->config['lang']['edit-title'] = $rootCat['name'] . " - " . $this->config['lang']['edit-title'];
        		$this->config['lang']['create-title'] = $rootCat['name'] . " - " . $this->config['lang']['create-title'];
        	}
        }
    }

    // Кнопка перехода в категорию, в данном случае в раздел
    protected function categoryButton($url_postfix) 
    {
    	return false;
    }

    protected function resourceCombineAfter($type)
    {
        if( array_search($type, ['store', 'update', 'destroy', 'sortable-save']) !== false ){
            Categories::clearAllCache();
        }
        parent::resourceCombineAfter($type);
    }

    protected function preSaveData($type) 
    {
    	// Удаляем лишние слеши
    	$this->post['url'] = trim(preg_replace("/\/{2,}/","/", $this->post['url']) , '\/');
    }

    // Убираем текущую запись при редактировании, что бы не создать рекурсию.
    protected function categoryGetFieldTree($rootCat, $exclude = '')
    {
        return parent::categoryGetFieldTree($rootCat, $this->post['id']);
    }

}
