<?php

namespace Backend\Root\Page\Controllers;

use Backend\Root\Page\Models\Page;
use Content;

class PageController extends \Backend\Root\Form\Controllers\ResourceController
{
   use \Backend\Root\Category\Services\Traits\Category;
    
    function __construct(Page $post)
    {
       parent::init($post);
    }

    public function update($id)
    {
    	//Игнорим текущую запись в валидации
        $this->fields['fields']['url']['validate'] .= ','.$id.',id,deleted_at,NULL';
        
        return parent::update($id);
    }
}