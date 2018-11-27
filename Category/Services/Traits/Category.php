<?php

namespace Backend\Root\Category\Services\Traits;

use Categories;
use Request;

trait Category {
    
    //Получаел локализацию
    public function localize()
    {
        //Если вызвано там гед уже есть локализация
        if(isset($this->config['lang'])) return false;

        if(! ($catID = Request::input('cat', false) )){
            if( !isset($this->post['category_id'] ))return false;
            $catID = $this->post['category_id'];
        }
        $this->config['lang'] = Categories::getRootCat($catID)['lang'];
    }

    //Проверка корректностикатегории
    protected function checkCategory($catID)
    {
        //Нексколько проверочек на корректность данных
        if( !$catID || ! ( $cat = Categories::getCat($catID)) ) 
            abort(403, 'Категории не существует');
        
        if($this->config['baseClass'] != 'Category'){
            if($this->config['baseClass'] != $cat['mod'])
                abort(403, 'Модуль категории не соответсвует модулю данных');
        }
        // else {
        //     if($type == 'update' && $this->post['mod'] != $cat['mod']) 
        //         abort(403, 'Модуль категории не соответсвует модулю родительской категории');
        // }

        return true;
    }

    protected function setCategoryList($type)
    {
        $catName = ($this->config['baseClass'] == 'Category') ? 'parent_id' : 'category_id';

        if( !isset($this->fields['fields'][$catName]) ) {
            $this->fields['fields'][$catName] = ['name' => $catName, 'type' => 'hidden'];
        }
        
        switch ($type) {
            case 'create':
                $catID = Request::input('cat', false); 
                $this->post[$catName] = $catID;
                break;
            case 'store': 
            case 'update': $catID = Request::input($catName, false); break;
            case 'edit': $catID = $this->post[$catName]; break;     
            default: $catID = false; break;       
        }

        $this->checkCategory($catID);

        //Если поле не селект, дерево не генерим.
        if($this->fields['fields'][$catName]['type'] != 'select') return;

        $exclude = ($catName == 'parent_id' && ($type == 'update' || $type == 'edit')) ? $this->post['id'] : '' ;
        $this->fields['fields'][$catName]['options'] = Categories::getHtmlTree([
            'empty' => true, 
            'root' => Categories::getRootCat($catID)['id'], 
            'exclude' => [ $exclude ],
            'offset' => '— ' 
        ]);
    }
}