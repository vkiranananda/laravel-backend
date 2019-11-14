<?php
namespace Backend\Root\Core\Services;
use Route;
use View;
use Cache;

class Backend { 
	
	private $data;

	public function init()
	{
		//43200
        $this->data = Cache::remember('backend-root-data', 43200, function() 
        {
        	$res = [];
        	$path['Backend\Root'] = base_path('vendor/vkiranananda/backend/');
        	$path['Backend'] = base_path('backend/');
		
	        foreach ($path as $namespace => $p) {
	        	$dir = opendir($p);
	        	while(false !==  ($file = readdir($dir)) ) {
	               if ($file == '..')continue;
	               if(is_dir($p.$file.'/resources') && is_dir($p.$file.'/resources/views') ) {
	               		$ext = (isset($res['views'][$file]) && $file != '.') ? '-ext' : '' ;
						$res['views'][$file.$ext] = $p.$file.'/resources/views';
	               }
	               if(is_file($p.$file.'/routes.php')){
	               		$res['routes'][$file][$namespace] = $p.$file.'/routes.php';
	               }
	            }
	        }
	        //Fix core resources
	        foreach (['views', 'routes'] as $key) {
		        if(isset($res[$key]['.'])){
		        	$res[$key]['Backend'] = $res[$key]['.'];
		        	unset($res[$key]['.']);
		        }
	        }
	        return $res;
	    });

        //Выставляем нейм спейсы. Если модуль наследуюется и в родителе уже есть namespace до дабавляем к наследуюемумо -ext. Если наследуется корневой resources тогда надо копировать его весь в свой каталог...
        if(isset($this->data['views'])){
	        foreach ($this->data['views'] as $mod => $p) {
	        	View::addNamespace($mod, $p);
	        }
	    }
	   //dd($this->data);
	}

	public function installRoutes($mod = '', $ext = [])
	{
		$modUrl = mb_strtolower($mod);
		if(!is_array($ext)){
			echo $mod;
			return;
		}
		if( array_search('upload', $ext) !== false ){
			Route::get($modUrl.'/upload/index/{id?}', '\Backend\\'.$mod.'\Controllers\UploadController@index');
			Route::post($modUrl.'/upload', '\Backend\\'.$mod.'\Controllers\UploadController@store');
			Route::delete($modUrl.'/upload/{id}', '\Backend\\'.$mod.'\Controllers\UploadController@destroy');
			Route::get($modUrl.'/upload/edit/{id?}', '\Backend\\'.$mod.'\Controllers\UploadController@edit');
			Route::put($modUrl.'/upload/update/{id?}', '\Backend\\'.$mod.'\Controllers\UploadController@update');
		}
		if( array_search('sortable', $ext) !== false ){
			Route::get($modUrl.'/sortable', '\Backend\\'.$mod.'\Controllers\\'.$mod.'Controller@listSortable');
			Route::put($modUrl.'/sortable', '\Backend\\'.$mod.'\Controllers\\'.$mod.'Controller@listSortableSave');
		}

		if ( isset($this->data['routes'][$mod]['Backend'] ) ){
			require_once($this->data['routes'][$mod]['Backend']);
		}
		else {
			Route::resource($modUrl, '\Backend\\'.$mod.'\\Controllers\\'.$mod.'Controller');
		}
	}

	//Подгружает роутинг из модуля
	public function loadBackendRoutes($mod = '')
	{
		if($mod == 'Backend') $mod = ''; 
		if(is_file(base_path('vendor/vkiranananda/backend/'.$mod.'routes.php')))
			require_once (base_path('vendor/vkiranananda/backend/'.$mod.'routes.php'));
	}
}
