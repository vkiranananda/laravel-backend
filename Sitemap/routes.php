<?php
	Route::get('/sitemap.xml', '\Backend\Root\Sitemap\Controllers\SitemapController@index');
	Route::get('sitemap/{url}.xml', '\Backend\Root\Sitemap\Controllers\SitemapController@index');
