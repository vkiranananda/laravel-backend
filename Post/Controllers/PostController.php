<?php

namespace Backend\Post\Controllers;

use Backend\Post\Models\Post;
use Content;

class PostController extends \Backend\Form\Services\ResourceController
{
    use \Backend\Category\Services\Traits\Category;
    use \Backend\Form\Services\Traits\RelationFields;
    
    function __construct(Post $post)
    {
       parent::init($post, 'Post::posts');
    }
}