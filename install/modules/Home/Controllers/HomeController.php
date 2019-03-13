<?php

namespace Backend\Home\Controllers;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
    	$params['title'] = 'Главная';
    	return view('Home::index',[ 'params' => $params ]  );
    }
}
