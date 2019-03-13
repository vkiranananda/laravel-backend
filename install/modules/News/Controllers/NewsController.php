<?php

namespace Backend\News\Controllers;

class NewsController extends \Backend\Root\Form\Controllers\ResourceController
{
    use \Backend\Root\Category\Services\Traits\Category;

    public $module = "Backend\Root\News\Models\News";

    public function update($id)
    {
    	//Игнорим текущую запись в валидации урла
        $this->fields['fields']['url']['validate'] .= ','.$id.',id,deleted_at,NULL';
        
        return parent::update($id);
    }
}