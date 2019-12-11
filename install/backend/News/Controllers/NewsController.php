<?php

namespace Backend\News\Controllers;

class NewsController extends \Backend\Root\Category\Controllers\CategoryResourceController
{
    public function update($id)
    {
    	//Игнорим текущую запись в валидации урла
        $this->fields['fields']['url']['validate'] .= ','.$id.',id,deleted_at,NULL';
        
        return parent::update($id);
    }
}