<?php

namespace Backend\Root\Category\Controllers;

use Backend\Root\Category\Models\Category;
use Request;
use Backend\Root\Core\Services\Helpers;
use Cache;
use Content;
use BackendConfig;

class CategoryRootController extends \Backend\Root\Form\Services\ResourceController
{
    function __construct(Category $post)
    {
       parent::init($post, 'Category::category-root');
    }
    
    protected function resourceCombine($type)
    {
        if($type == 'create' || $type == 'store'){

            $modules = [];

            foreach ( BackendConfig::get('category-modules') as $key => $value) {
                $modules[$key]['value'] = $key;
                $modules[$key]['label'] = $value['label'];
            }
            $this->params['fields']['mod']['options'] = $modules;
        }elseif($type == 'update' || $type == 'edit') {
            unset($this->params['fields']['mod']);
        }
        
        // if( ($type == 'update' || $type == 'store') && ! Request::has('url') ){
        //     $this->params['tabs']['default']['fields']['url']['conf-validate'] = '';
        // }
    }
    protected function resourceCombineAfter($type)
    {
        if( array_search($type, ['store', 'update', 'destroy']) !== false ){
            Cache::tags('category')->flush();
        }
    }

    protected function getViewUrl()
    {
        $this->post->parent_id = 0;
        return Content::getUrl($this->post);
    }
}