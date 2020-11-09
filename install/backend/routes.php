<?php

// if (app()->runningInConsole()) return;

Route::group(['prefix' => 'content', 'middleware' => ['auth.basic'] ], function()
{
	Route::get('/', '\Backend\Home\Controllers\HomeController@index');
	Backend::installRoutes('Category', ['upload', 'module']);
	Backend::installRoutes('News', ['upload']);
	Backend::installRoutes('Page', ['upload']);
	Backend::installRoutes('User', ['upload', 'module']);
	Backend::installRoutes('Option', ['upload']);
	Backend::installRoutes('MenuBuilder');
});
