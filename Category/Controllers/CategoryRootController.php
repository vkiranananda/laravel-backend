<?php

namespace Backend\Root\Category\Controllers;

use Backend\Root\Category\Models\Category;
use Request;
use Helpers;
use Cache;
use Content;
use GetConfig;

class CategoryRootController extends \Backend\Root\Form\Controllers\ResourceController
{
    protected $configPath = "Category::config-root";
    protected $fieldsPath = "Category::fields-root";

    function __construct(Category $post)
    {
    	parent::init($post);
    }
    
    protected function resourceCombine($type)
    {
        if ( array_search($type, ['create', 'store', 'edit', 'index']) !== false ) {

            $modules = [];
            //Получаем список доступных модулей для категорий
            foreach ( GetConfig::backend('category-modules') as $key => $value) {
                $modules[] = [ 
                	'value' => $key,
                	'label' => $value['label']
                ];
            }
            $this->fields['fields']['mod']['options'] = $modules;
        } 

        //Для сохранения убираем поле, что бы не изменть значние
        elseif ($type == 'update') { 
        	unset( $this->fields['fields']['mod'] );
        }

        //Делаем поле неактивным, так как модуль нельзя поменять
        if ( $type == 'edit') { 
        	//Убираем поле 'mod'.
        	$this->fields['fields']['mod']['attr'] = ['disabled' => true];
        } 
        
        // if( ($type == 'update' || $type == 'store') && ! Request::has('url') ){
        //     $this->params['fields']['url']['conf-validate'] = '';
        // }
    }
    protected function resourceCombineAfter($type)
    {
    	// Очидаем кэши
        if ( array_search($type, ['store', 'update', 'destroy']) !== false ) {
            Cache::tags('category')->flush();
        }
    }

    protected function getViewUrl()
    {
        return Content::getUrl($this->post);
    }
}
