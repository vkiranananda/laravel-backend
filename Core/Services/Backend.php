<?php
namespace Backend\Core\Services;
use Route;

class Backend { 

	static public function installRoutes($mod = '', $upload = false)
	{

		// static $routes = false;

		$path = base_path().'/backend/';
		
		$modUrl = mb_strtolower($mod);

		if($mod == ''){
			require_once($path.'routes.php');
			return;
		}
		
		if($upload){
			Route::get($modUrl.'/gallery/index/{id?}', '\Backend\\'.$mod.'\Controllers\UploadController@index');
			Route::post($modUrl.'/gallery', '\Backend\\'.$mod.'\Controllers\UploadController@store');
			Route::delete($modUrl.'/gallery/{id}', '\Backend\\'.$mod.'\Controllers\UploadController@destroy');
		}

		if ( is_file($path.$mod.'/routes.php') )
			require_once($path.$mod.'/routes.php');
		elseif( is_file($path.$mod.'/Controllers/'.$mod.'Controller.php' ) ) {
			Route::resource($modUrl, '\Backend\\'.$mod.'\\Controllers\\'.$mod.'Controller');
		}
	}

}