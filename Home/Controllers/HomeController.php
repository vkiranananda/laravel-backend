<?php

namespace Backend\Root\Home\Controllers;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function main()
    {
    	$params['title'] = 'Главная';
    	return view('Backend::home',[ 'params' => $params ]  );
    }
}
