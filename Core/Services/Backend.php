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
<<<<<<< HEAD
        	$path['Backend\Root'] = base_path('vendor/vkiranananda/backend/');
        	$path['Backend'] = base_path('backend/');
=======
        	$path['Backend'] = base_path('vendor/vkiranananda/backend/');
        	$path['Backend\Root\Ext'] = base_path('backend/');
>>>>>>> 335b97e178203b3721db194e913a3e19b7c70ee0
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

<<<<<<< HEAD
	   // dd($this->data);
=======
	  //  dd($this->data);
>>>>>>> 335b97e178203b3721db194e913a3e19b7c70ee0
	}

	public function installRoutes($mod = '', $namespace = 'Backend', $upload = false)
	{

<<<<<<< HEAD
		if($namespace == 'Root') $namespace  =  'Backend\Root';

=======
		// static $routes = false;	
>>>>>>> 335b97e178203b3721db194e913a3e19b7c70ee0
		$modUrl = mb_strtolower($mod);
		
		if($upload){
			Route::get($modUrl.'/gallery/index/{id?}', '\\'.$namespace.'\\'.$mod.'\Controllers\UploadController@index');
			Route::post($modUrl.'/gallery', '\\'.$namespace.'\\'.$mod.'\Controllers\UploadController@store');
			Route::delete($modUrl.'/gallery/{id}', '\\'.$namespace.'\\'.$mod.'\Controllers\UploadController@destroy');
		}

		if ( isset($this->data['routes'][$mod][$namespace] ) ){
			require_once($this->data['routes'][$mod][$namespace]);
		}
		else {
			Route::resource($modUrl, '\\'.$namespace.'\\'.$mod.'\\Controllers\\'.$mod.'Controller');
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