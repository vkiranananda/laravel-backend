<?php

namespace Backend\Root\Category\Controllers;

use Categories;
use Session;
use Backend\Root\Category\Models\Category;
use Request;
use Cache;
use Content;

class CategoryController extends \Backend\Root\Form\Services\ResourceController
{
    use \Backend\Root\Category\Services\Traits\Category;

    function __construct(Category $post)
    {
       parent::init($post, 'Category::category');
    }

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

    public function treeExpand($id, $status)
    {
        if($status == 1)Session::put('config.category.tree.'.$id, '1');
        else Session::forget('config.category.tree.'.$id);
    }

    public function destroy($id)
    {
        if( !( $cat = Categories::getCat($id) ) )abort(403, 'Удаляем категорию, категории не существует');
        $cats = Categories::getListIds($id, true);
        $this->post->destroy($cats);
        Cache::tags('category')->flush();
    }
}