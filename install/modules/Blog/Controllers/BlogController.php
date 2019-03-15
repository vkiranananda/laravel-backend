<?php

namespace Backend\Blog\Controllers;

class BlogController extends \Backend\Root\Form\Controllers\ResourceController
//\Backend\Category\Controllers\CategoryResourceController
{
    public $module = 'Backend\Blog\Models\Blog';

    public function update($id)
    {
    	//Игнорим текущую запись в валидации урла
        $this->fields['fields']['url']['validate'] .= ','.$id.',id,deleted_at,NULL';
        
        return parent::update($id);
    }
}