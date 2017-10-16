<?php

namespace Backend\Category\Services;

use Backend\Category\Models\Category;
use Backend\Core\Services\Helpers;
use Route;
use Cache;
use BackendConfig;

class Categories
{
	private $allCat = [];
	private $moduleControllers = [];

	function __construct()
	{
		$this->init();
	}

	public function init($showDeleted = false)
	{

        $allCat = Cache::tags('category')->remember('allCat', 43200, function() use ($showDeleted)
        {
        	$allCat = [];

			$cats = ($showDeleted) ? Category::onlyTrashed()->orderBy('deleted_at', 'desc')->get() : Category::orderBy('sort_num')->get();

			foreach ($cats as $cat) {
				$id = $cat['id'];
				$allCat[$id] = Helpers::setArray($cat, ['name', 'id', 'url', 'parent_id', 'mod']);
				if($cat['parent_id'] == 0 && is_array($cat['array_data']['fields'])){
					foreach ($cat['array_data']['fields'] as $key => $el) {
						if(preg_match("/^conf-(.*)/", $key, $matches)){
							$allCat[$id]['conf'][$matches[1]] = $el;
						}elseif(preg_match('/^lang-(.*)/', $key, $matches)){
							$allCat[$id]['lang'][$matches[1]] = $el;
						}
					}
				}
			}
			return $allCat;
        }); 

		if($showDeleted)return $allCat;
		else $this->allCat = $allCat;

		$this->moduleControllers = BackendConfig::get('category-modules', true);
	}

	public function getModuleControllers($mod)
	{
		return $this->moduleControllers[$mod];
	}

	//Устанавливаем роуты
	public function installRoutes()
	{
		Route::get('{url}', '\Backend\Category\Controllers\RouteController@index')->where('url', '.+');
	}

	//Количество категорий
	public function count()
	{
		return count($this->allCat);
	}
	//Выводим роуты
	public function printRoutes()
	{
		$curClass = &$this;

		return Cache::tags('category')->remember('routes', 43200, function() use ($curClass)
        {	
        	$routes = [];
			$curClass->getRoutes($routes);

			return $routes;
        }); 
	}

	//Генерим все роуты
	private function getRoutes( &$res, $parent_id = 0, $url = '')
	{
		foreach ($this->allCat as $cat) {
			$newUrl = $url;
			if($cat['parent_id'] == $parent_id){
				if($parent_id == 0)	$newUrl = '';
				if($cat['url'] != '') $newUrl .= '/'.$cat['url'];
				
				$res[$newUrl][$cat['mod']][] = $cat['id'];
				$this->getRoutes($res, $cat['id'], $newUrl);
			}
		}
	}


	//Выводим путь до нужной категории
	public function getPath($catID)
	{
		$res = [];
		$this->_getPath($catID, $res);
		return $res;
	}

	private function _getPath($catID, &$res)
	{
		if(isset( $this->allCat[$catID] ) ){
			if( $this->allCat[$catID]['parent_id'] != 0 ){
				$this->_getPath($this->allCat[$catID]['parent_id'] , $res);
			}
			$res[] = $this->allCat[$catID];
		}	
	}


	//Получаем ID корневой записи для post_type
	public function getRootCat($catID)
	{
		if(!isset($this->allCat[$catID]))abort(403, 'Categories::getRootCat() категории не существует');
	
		if( $this->allCat[$catID]['parent_id'] == 0 )return $this->allCat[$catID];
		else return $this->getRootCat($this->allCat[$catID]['parent_id']);
	}

	//Получаем категорию, если false выводим все
	public function getCat($id = false){
		//Выводим все категории
		if(!$id)return $this->allCat;
		//Если категории нет
		if(!isset($this->allCat[$id]))return false;
		//Выводим нужную категорию
		return $this->allCat[$id];
	}



	//Гереним дерево
	public function getHtmlTree($pref)
	{
		$res = [];
		$this->genTree($res, $pref, $pref['root']);
		$res = Helpers::getHtmlOptions($res);
		if(isset($pref['empty'])) array_unshift($res,  ['value' => $pref['root'], 'label' => '']);

		return $res;
	}

	//получаем список id шников вложенной в указанную категорию
	public function getListIds($rootCat = 0, $resCur = false)
	{
		$res = [];
		$pref['root'] = $rootCat;
		$this->genTree($res, $pref, $rootCat);
		$res = Helpers::getListIds($res);
		if($resCur)array_unshift($res, $rootCat);
		
		return  $res;
	}

	//Рекурсивная функция генерит дерево.
	private function genTree(&$res, &$pref, $parentId = 0, $offset = '')
	{
		//Вычисляем смещение если запись не корневая
		if( $parentId != $pref['root'] ) {
			if( isset($pref['offset']) )$offset .= $pref['offset'];
			elseif ( isset($pref['offset_key']) ){
				$offset++;
			}
		}

		foreach ($this->allCat as $id => $cat) {
			if( $cat['parent_id'] != $parentId ) continue;
			if( isset( $pref['exclude'] ) && is_array( $pref['exclude'] ) && array_search($id, $pref['exclude'] ) !== false) continue;

			//Устанавливаем смещение, либо доп поле либо префиксы
			if(isset($pref['offset_key']) ) $catRes['c_tree_offset'] = $offset;
			elseif ( isset($pref['offset']) ){
				$cat['name'] = $offset.$cat['name'] ;
			}

			$res[] = $cat;

			$this->genTree($res, $pref, $id, $offset);
		}
	}
}

?>