<?php

namespace Backend\Root\Category\Controllers;

use App\Http\Controllers\Controller;
use Categories;
use Backend\Root\User\Services\UserAccess;

class InfoController extends Controller
{
    public function routes()
    {
        if (!UserAccess::checkAccess('read-all', 'CategoryRoutingRead')) {
            abort(403, 'Нет доступа к роутингу');
        }
    	$params['title'] = 'Список маршрутов';
    	return view('Category::routes',[ 'data' => Categories::printRoutes() , 'params' => $params ] );
    }
}
