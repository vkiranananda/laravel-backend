<?php

namespace Backend\Page\Controllers;

use Backend\Page\Models\Page;
use Content;

class PageController extends \Backend\Form\Services\ResourceController
{
    use \Backend\Category\Services\Traits\Category;
    
    function __construct(Page $post)
    {
       parent::init($post, 'Page::pages');
    }
}