<?php

use Backend\Root\Core\Services\Backend;

Backend::installRoutes('Sitemap');

Route::group(['prefix' => 'control', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Root\Home\Controllers\HomeController@admin');
<<<<<<< HEAD
	Backend::installRoutes('User\Root');
	Backend::installRoutes('Option\Root', 'Backend', true);
=======
	Backend::installRoutes('User');
	Backend::installRoutes('Option', 'Backend', true);
>>>>>>> 335b97e178203b3721db194e913a3e19b7c70ee0
	Backend::installRoutes('Ad', 'Backend', true);
});

Route::group(['prefix' => 'content', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Root\Home\Controllers\HomeController@content');

<<<<<<< HEAD
	Backend::installRoutes('Category', 'Backend\Root', true);
	Backend::installRoutes('Page', 'Backend\Root', true);
	Backend::installRoutes('News', 'Backend\Root', true);
	Backend::installRoutes('MediaFile', 'Backend\Root', true);
	Backend::installRoutes('Property', 'Backend', true);
	Backend::installRoutes('Excursion', 'Backend', true);
	Backend::installRoutes('Realty', 'Backend', true);
	Backend::installRoutes('Upload\Root');
=======
	Backend::installRoutes('Category', 'Backend', true);
	Backend::installRoutes('Page', 'Backend', true);
	Backend::installRoutes('News', 'Backend', true);
	Backend::installRoutes('MediaFile', 'Backend', true);
	Backend::installRoutes('Property', 'Backend', true);
	Backend::installRoutes('Excursion', 'Backend', true);
	Backend::installRoutes('Realty', 'Backend', true);
	Backend::installRoutes('Upload');
>>>>>>> 335b97e178203b3721db194e913a3e19b7c70ee0
});

Route::group(['prefix' => 'utils', 'middleware' => 'auth.basic'], function()
{
	Route::get('/', '\Backend\Root\Home\Controllers\HomeController@utils');
	Route::get('routes', '\Backend\Root\Info\Controllers\InfoController@routes');
	
	Backend::installRoutes('Redirect');
});

Categories::installRoutes();



