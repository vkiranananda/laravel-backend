<?php

namespace Backend\Root\Category\Controllers;

use GetConfig;
use Categories;
use Helpers;

class CategoryRootController extends \Backend\Root\Form\Controllers\ResourceController
{


    protected $configPath = "Category::config-root";
    protected $fieldsPath = "Category::fields-root";

    // Выборка только корневых записей
    public function index()
    {
    	$this->post = $this->post->where('category_id', '0');

    	return parent::index();
    }

	public function indexTreeField($post, $field) {
		if (Helpers::getDataField($post, 'conf-type', '') != '') return "Категории";
		else return "";
	}

    // Выборка только корневых записей для сортировки
    protected function listSortable()
    {
    	$this->post = $this->post->where('category_id', '0');

    	return parent::listSortable();
    }

    // Обрабатываем ссылки в списке
    protected function indexLinks($post, $urlPostfix) {
    	$res = parent::indexLinks($post, $urlPostfix);
    	if (Helpers::getDataField($post, 'conf-type', false) == 'hierarchical'){
    		$res['category'] = action($this->config['base-namespace'].'Controllers\CategoryController@index').'?cat='.$post['id'];
    	}

    	return $res;
    }

    protected function resourceCombine($type)
    {
        if ( $type != 'destroy' ) {

            $modules = [];
            // Получаем список доступных модулей для категорий
            foreach ( GetConfig::backend('Category::modules') as $key => $value) {
                $modules[] = [
                	'value' => $key,
                	'label' => $value['label']
                ];
            }
        	$this->fields['fields']['mod']['options'] = $modules;
        }

        // Делаем поле неактивным, так как модуль нельзя поменять
        if ( $type == 'edit') {
        	$this->fields['fields']['mod']['attr'] = ['disabled' => true];
        }
    }

    protected function preSaveData($type)
    {
    	// Удаляем лишние слеши
    	$this->post['url'] = trim(preg_replace("/\/{2,}/","/", $this->post['url']) , '\/');
    }

    protected function resourceCombineAfter($type)
    {
    	// Очищаем кэши
        if ( array_search($type, ['store', 'update', 'destroy', 'sortable-save']) !== false ) {
        	// Удаляем кэши
            Categories::clearAllCache();

            // Обновляем данные с категориями.
            Categories::init();
            // Выставляем хук для обновления списка категорий
            $this->dataReturn['hook'] = [
            	'name' => 'MainMenuUpdate',
            	'data' => [
            		'name'	=> 'category',
            		'items' => \Backend\Root\Category\Services\Menu::getCats()
            	]
            ];
        }
    }

    protected function getViewUrl()
    {
    	// Изначально подставляется в бд, при создании новой записи, запрос в бд не делается и значние пустой, поэтому надо тут выставить что бы не было ошибки :).
    	$this->post['category_id'] = 0;
        return Categories::getUrl($this->post);
    }
}
