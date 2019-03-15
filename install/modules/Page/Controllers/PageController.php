<?php

namespace Backend\Page\Controllers;

//\Backend\Category\Controllers\CategoryResourceController
class PageController extends \Backend\Root\Form\Controllers\ResourceController
{
    public $module  = 'Backend\Page\Models\Page';

    public function update($id)
    {
    	//Игнорим текущую запись в валидации урла
        $this->fields['fields']['url']['validate'] .= ','.$id.',id,deleted_at,NULL';
        
        return parent::update($id);
    }
}