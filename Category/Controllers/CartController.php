<?php

namespace Backend\Root\Category\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Categories;
use Backend\Root\Category\Models\Category;
use Backend\Root\Core\Services\Helpers;

class CartController extends Controller
{
	private $posts;

    public function index(Request $request)
    {
		$allCat = [];
		foreach (Category::onlyTrashed()->orderBy('deleted_at', 'desc')->get() as $cat) {
			// echo $cat['deleted_at']." ";
			$allCat[ $cat['id'] ] = Helpers::setArray($cat, ['name', 'id', 'url', 'parent_id', 'mod','deleted_at']);
		}

		$newCats = [];
    	foreach ($allCat as $id => $cat) {

    		$cat['depth'] = 0;
    		while(isset($allCat[$cat['parent_id']])){
    			$cat = $allCat[$cat['parent_id']];
    		}

    		$newCats[$cat['id']] = $cat;

    		$this->getCats($newCats, $allCat, $cat['id']);
    	}



    	//Добавляем  отсупы
    	$depth = 0;
    	foreach ($newCats as $key => $el) {
    		if(isset($el['depth'])) $depth = $el['depth'];
    		$newCats[$key]['name'] = '<span class="icons-directory"></span>&nbsp;'.$el['name'];
    		if($depth != 0)$newCats[$key]['name'] = '<span style="padding-right: '.($depth*25).'px"></span>'.$newCats[$key]['name'];
    	}

                // dd($newCats);

    	return view('Category::cart', [ 'dataCats' => $newCats, 'params' => GetConfig::backend('Category::cart') ] );
    }


    private function repeatStr($str, $count = 0)
    {
    	$res = '';
    	for($i = 0; $i < $count; $i++){
    		$res .= $str;
    	}
    	return $res;
    }

    private function getCats(&$res, &$cats, $parent_id, $depth = 1)
    {
    	foreach ($cats as $id => $cat) {
    		if($cat['parent_id'] == $parent_id){
    			$cat['depth'] = $depth;
    			$res[$id] = $cat;
    			$this->getCats($res, $cats, $cat['id'], ($depth+1));
    		}
    	}
    }
 
}
