<?php

namespace Backend\Home\Controllers;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function content()
    {
    	$params['title'] = 'Главная';
    	return view('Backend::home',[ 'params' => $params ]  );
    }
    
    public function admin()
    {
    	$params['title'] = 'Главная';
    	return view('Backend::home',[ 'params' => $params ]  );
    }

    public function utils()
    {
    	$params['title'] = 'Главная';
    	return view('Backend::home',[ 'params' => $params ]  );
    }
}
