<?php

namespace Backend\Root\Category\Controllers;
use Categories;

class RouteController
{
    public function index($url)
    {
		$routes = Categories::printRoutes();

		$url = '/'.$url;

		// Категории, точное совпадение
		if (isset($routes[$url]) ){
			$cat = $routes[$url];
			$class = Categories::getModuleControllers(key($cat))['categoryController'];
			$c = new $class;
			return $c->index(reset($cat));
		} else {
			// Записи внутри, если записи с данным урлом нет, проверяем запись с вложенным урлом
			$resUrl = '';
			while (preg_match("/^(.*)\/(.+)$/", $url, $urlExt)) {
				// Весь урл до последнего сегмента
				$url = $urlExt[1];
				// Создаем конечный путь, может быть вложенным
				$resUrl = ($resUrl != '') ? $urlExt[2] . "/" . $resUrl : $urlExt[2];

				// Если урл есть 
				if (isset($routes[$url])) {
					$cat = $routes[$url];
					// Корнева каотеогрия, получаем контроллер
					$class = Categories::getModuleControllers(key($cat))['viewControllerAction'];
					$c = new $class;

					return $c->show(reset($cat), $resUrl);
				}
			}
		}
		abort(404);
    }
}
