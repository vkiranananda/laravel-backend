<?php
namespace Backend\Root\Core\Services;
use Route;
use View;
use Cache;

class Backend { 
	
	private $data;

	public function init()
	{
        $this->data = Cache::tags('backend')->remember('data', 43200, function() 
        {
        	$res = [];
        	$path['Backend\Root'] = base_path('backend-root/');
        	$path['Backend'] = base_path('backend/');
		
	        foreach ($path as $namespace => $p) {
	        	$dir = opendir($p);
	        	while(false !==  ($file = readdir($dir)) ) {
	               if ($file == '..')continue;
	               if( is_dir($p.$file.'/resources/views') ) {
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

	public function installRoutes($mod = '', $namespace = 'Backend', $upload = false)
	{
		if($namespace == 'Root') $namespace  =  'Backend\Root';

		$modUrl = mb_strtolower($mod);
		
		if($upload){
			Route::get($modUrl.'/gallery/index/{id?}', '\\'.$namespace.'\\'.$mod.'\Controllers\UploadController@index');
			Route::post($modUrl.'/gallery', '\\'.$namespace.'\\'.$mod.'\Controllers\UploadController@store');
			Route::delete($modUrl.'/gallery/{id}', '\\'.$namespace.'\\'.$mod.'\Controllers\UploadController@destroy');
		}
//dd($this->data);
		if ( isset($this->data['routes'][$mod][$namespace] ) ){
			require_once($this->data['routes'][$mod][$namespace]);
			//echo $this->data['routes'][$mod][$namespace];
		}
		else {
			Route::resource($modUrl, '\\'.$namespace.'\\'.$mod.'\\Controllers\\'.$mod.'Controller');
		}
	}

	//Подгружает роутинг из модуля
	public function loadBackendRoutes($mod = '')
	{
		if($mod == 'Backend') $mod = ''; 
		if(is_file(base_path('backend-root/'.$mod.'routes.php')))
			require_once (base_path('backend-root/'.$mod.'routes.php'));
	}
}
