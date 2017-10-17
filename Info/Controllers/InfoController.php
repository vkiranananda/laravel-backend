<?php

namespace Backend\Root\Info\Controllers;

use App\Http\Controllers\Controller;
use Categories;

class InfoController extends Controller
{
    public function routes()
    {
    	$params['title'] = 'Список маршрутов';
    	return view('Info::routes',[ 'data' => Categories::printRoutes() , 'params' => $params ] );
    }
}
