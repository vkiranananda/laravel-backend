<?php

namespace Backend\Root\Category\Controllers;

use Backend\Root\Category\Models\Category;
use Cache;
use Content;
use GetConfig;
use Categories;

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
            // Получаем список доступных модулей для категорий
            foreach ( GetConfig::backend('category-modules') as $key => $value) {
                $modules[] = [ 
                	'value' => $key,
                	'label' => $value['label']
                ];
            }
            $this->fields['fields']['mod']['options'] = $modules;
        } 

       
        elseif ($type == 'update') { 
    		// Игнорим текущую запись в валидации
        	$this->fields['fields']['url']['validate'] .= ','.$this->post['id'].',id,deleted_at,NULL';
        	// Для сохранения убираем поле, что бы не изменть значние
        	unset( $this->fields['fields']['mod'] );
        }

        // Делаем поле неактивным, так как модуль нельзя поменять
        if ( $type == 'edit') { 
        	$this->fields['fields']['mod']['attr'] = ['disabled' => true];
        } 
    }
    protected function resourceCombineAfter($type)
    {
    	// Очищаем кэши
        if ( array_search($type, ['store', 'update', 'destroy']) !== false ) {
            Cache::tags('category')->flush();

            // Обновляем данные с категориями.
            Categories::init();

            //Выставляем хук для обновления списка категорий
            $this->dataReturn['hook'] = [
            	'name' => 'MenuCategoryRootReload',
            	'data' => \Backend\Root\Category\Services\Menu::getCats()
            ];
        }
    }

    protected function getViewUrl()
    {
    	// Изначально подставляется в бд, при создании новой записи, запрос в бд не делается и значние пустой, поэтому надо тут выставить что бы не было ошибки :).
    	$this->post['category_id'] = 0;
        return Content::getUrl($this->post);
    }
}
