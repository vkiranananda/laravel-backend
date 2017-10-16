<?php

use Backend\Core\Services\Backend;

Backend::installRoutes('Sitemap');

Route::group(['prefix' => 'control', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Home\Controllers\HomeController@admin');
	Backend::installRoutes('User');
	Backend::installRoutes('Option', 'Backend', true);
	Backend::installRoutes('Ad', 'Backend', true);
});

Route::group(['prefix' => 'content', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Home\Controllers\HomeController@content');

	Backend::installRoutes('Category', 'Backend', true);
	Backend::installRoutes('Page', 'Backend', true);
	Backend::installRoutes('News', 'Backend', true);
	Backend::installRoutes('MediaFile', 'Backend', true);
	Backend::installRoutes('Property', 'Backend', true);
	Backend::installRoutes('Excursion', 'Backend', true);
	Backend::installRoutes('Realty', 'Backend', true);
	Backend::installRoutes('Upload');
});

Route::group(['prefix' => 'utils', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Home\Controllers\HomeController@utils');
	Route::get('routes', '\Backend\Info\Controllers\InfoController@routes');
	
	Backend::installRoutes('Redirect');
});

Categories::installRoutes();



