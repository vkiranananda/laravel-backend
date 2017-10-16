<?php

namespace Backend\Category\Controllers;
use Categories;
use Cache;

class RouteController
{
    public function index($url)
    {
		$routes = Categories::printRoutes();

		$url = '/'.$url;

		if(isset($routes[$url]) ){
			$cat = $routes[$url];
			$class = Categories::getModuleControllers(key($cat))['categoryController'];
			$c = new $class;
			return $c->index(reset($cat));
		}else{
			$catUrl = "/";
			if( preg_match("/(^.*)\/(.+)$/", $url, $urlExt) && isset($routes[$urlExt[1]]) ) {
				$cat = $routes[$urlExt[1]];
				$class = Categories::getModuleControllers(key($cat))['viewControllerAction'];
				$c = new $class;
				return $c->show(reset($cat), $urlExt[2]);
			}
		}
    }
}
