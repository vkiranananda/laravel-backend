<?php

namespace Backend\Category\Controllers;

use Request;
use Cache;
use Categories;

class CategoryController extends \Backend\Category\Controllers\CategoryResourceController
{
    public function store()
    {
    	// Выставлеям модуль из корневой категории
        $this->post['mod'] = Categories::getRootCat(Request::input('fields', [])['category_id'])['mod'];

        return parent::store();
    }

    // Обновляем запись
    public function update($id)
    {
		// Игнорим текущую запись в валидации
    	$this->fields['fields']['url']['validate'] .= ','.$this->post['id'].',id,deleted_at,NULL';

    	return parent::update($id);
    }

    protected function resourceCombineAfter($type)
    {
        if( array_search($type, ['store', 'update', 'destroy', 'sortable-save']) !== false ){
            Cache::tags('category')->flush();
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
