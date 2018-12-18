<?php

namespace Backend\Root\Category\Services\Traits;

use Categories;
use Request;

trait Category {
    
    //Получаел локализацию
    public function localize()
    {
        //Если вызвано там где уже есть локализация
        if ( isset($this->config['lang']) ) return false;

        if (! ($catID = Request::input('cat', false) ) ) {
            if (! isset($this->post['category_id'] ) ) return false;
            $catID = $this->post['category_id'];
        }
        $this->config['lang'] = Categories::getRootCat($catID)['lang'];
    }

    //Проверка корректностикатегории
    protected function checkCategory($catID)
    {    	
    	$cat = Categories::getCat($catID);

        //Несколько проверочек на корректность данных
        if ( $catID === false || $cat === false ) abort(403, 'Категории "'.$catID.'" не существует');
        
        if ($this->config['baseClass'] != 'Category') {
            if ($this->config['baseClass'] != $cat['mod'])
                abort(403, 'Модуль категории не соответсвует модулю данных');
        }

        return true;
    }

    protected function setCategoryList($type)
    {

    	// Если поле скрыто, то выставляем как true
    	$hidden = false;
        
        // Если поле категории не установлено добавляем его в скрытые поля
        if ( !isset($this->fields['fields']['category_id']) ) {
            $this->fields['hidden'][] = [ 
            	'name' 	=> 'category_id', 
            ];

            $hidden = true;
        }

        // Получаем значение категории
        switch ($type) {
            case 'create':
                $catID = Request::input('cat', false); 
                $this->post['category_id'] = $catID;
                break;
            case 'store': 
            case 'update': 
            	$catID = ($hidden) ? Request::input('hidden')['category_id'] : Request::input('fields')['category_id'];
            	break;
            case 'edit': $catID = $this->post['category_id']; break;     
            default: $catID = false; break;       
        }

        $this->checkCategory($catID);

        //Если поле скрытое, далее не обрабатываем
        if ($hidden) return;

        //Если поле не селект, дерево не генерим.
        if ($this->fields['fields']['category_id']['type'] != 'select') {
        	abort(403, 'setCategoryList: Поле category_id должно быть select');
        	return;
        }

        $exclude = ('category_id' == 'parent_id' && ($type == 'update' || $type == 'edit')) ? $this->post['id'] : '' ;

        $this->fields['fields']['category_id']['options'] = Categories::getHtmlTree([
            'empty' => true, 
            'root' => Categories::getRootCat($catID)['id'], 
            'exclude' => [ $exclude ],
            'offset' => '— ' 
        ]);
    }
}