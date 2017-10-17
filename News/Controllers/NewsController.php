<?php

namespace Backend\Root\News\Controllers;

use Backend\Root\News\Models\News;
use Cache;
use Content;

class NewsController extends \Backend\Root\Form\Services\ResourceController
{
    use \Backend\Root\Category\Services\Traits\Category;

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