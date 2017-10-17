<?php

namespace Backend\Root\Page\Controllers;

use Backend\Root\Page\Models\Page;
use Content;

class PageController extends \Backend\Root\Form\Services\ResourceController
{
    use \Backend\Root\Category\Services\Traits\Category;
    
    function __construct(Page $post)
    {
       parent::init($post, 'Page::pages');
    }
}