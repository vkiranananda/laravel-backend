<?php

use Backend\Core\Services\Backend;

Backend::installRoutes('Sitemap');

Route::group(['prefix' => 'control', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Home\Controllers\HomeController@admin');
	Backend::installRoutes('User');
	Backend::installRoutes('Option', true);
	Backend::installRoutes('Ad', true);
});

Route::group(['prefix' => 'content', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Home\Controllers\HomeController@content');

	Backend::installRoutes('Category', true);
	Backend::installRoutes('Post', true);
	Backend::installRoutes('Page', true);
	Backend::installRoutes('News', true);
	Backend::installRoutes('MediaFile', true);
	Backend::installRoutes('Property', true);
	Backend::installRoutes('Excursion', true);
	Backend::installRoutes('Realty', true);
	Backend::installRoutes('Upload');

});

Route::group(['prefix' => 'utils', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Home\Controllers\HomeController@utils');
	Route::get('routes', '\Backend\Info\Controllers\InfoController@routes');
	
	Backend::installRoutes('Redirect');
});

Categories::installRoutes();



