<?php

namespace Backend\Root\Blog\Controllers;

use Backend\Root\Blog\Models\Blog;
use Cache;
use Content;

class BlogController extends \Backend\Root\Form\Controllers\ResourceController
{
    use \Backend\Root\Category\Services\Traits\Category;

    function __construct(Blog $post)
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