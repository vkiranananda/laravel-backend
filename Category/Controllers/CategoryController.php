<?php

namespace Backend\Root\Category\Controllers;

use Categories;
use Session;
use Request;
use Cache;
use Content;

class CategoryController extends \Backend\Root\Form\Controllers\ResourceController
{
    use \Backend\Root\Category\Services\Traits\Category;

    public $module = 'Backend\Root\Category\Models\Category';

    public function store()
    {
     
        $this->post['mod'] = Categories::getRootCat(Request::input('parent_id', false))['mod'];
  
        return parent::store();
    }

    protected function resourceCombineAfter($type)
    {
        if( array_search($type, ['store', 'update', 'destroy']) !== false ){
            Cache::tags('category')->flush();
        }
    }

    public function destroy($id)
    {
        if( !( $cat = Categories::getCat($id) ) )abort(403, 'Удаляем категорию, категории не существует');
        $cats = Categories::getListIds($id, true);
        $this->post->destroy($cats);
        Cache::tags('category')->flush();
    }
}