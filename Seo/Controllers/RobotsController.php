<?php

namespace Backend\Root\Seo\Controllers;

use App\Http\Controllers\Controller;

class RobotsController extends Controller
{
    public function index()
    {
    	return response()
            ->view('Seo::robots', [], 200)
            ->header('Content-Type', 'text/plain');
    }
}
