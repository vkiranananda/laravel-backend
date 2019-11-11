<?php

namespace Backend\Category\Controllers;

use App\Http\Controllers\Controller;
use Categories;

class InfoController extends Controller
{
    public function routes()
    {
    	$params['title'] = 'Список маршрутов';
    	return view('Category::routes',[ 'data' => Categories::printRoutes() , 'params' => $params ] );
    }
}
