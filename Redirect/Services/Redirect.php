<?php
namespace Backend\Root\Redirect\Services;
use Request;
// use Redirect;

class Redirect { 
	public static function installRoutes()
	{
		$route = \Backend\Root\Redirect\Models\Redirect::where('enable', '1')->where('from_url', '/'.Request::path())->first();
		if( $route ){
			echo redirect($route['to_url'], 301); 
			exit;
		}
	}
}