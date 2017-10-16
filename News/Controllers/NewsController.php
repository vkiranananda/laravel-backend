<?php

namespace Backend\News\Controllers;

use Backend\News\Models\News;
use Cache;
use Content;

class NewsController extends \Backend\Form\Services\ResourceController
{
    use \Backend\Category\Services\Traits\Category;

    function __construct(News $post)
    {
       parent::init($post, 'News::news');
    }

    protected function resourceCombine($type)
    {
        parent::resourceCombine($type);

        if( array_search($type, ['store', 'update', 'destroy']) !== false ){
            Cache::tags('news-widgets')->flush();
        }
    }
}