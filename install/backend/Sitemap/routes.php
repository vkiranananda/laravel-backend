<?php
	Route::get('/sitemap.xml', '\Backend\Sitemap\Controllers\SitemapController@index');
	Route::get('sitemap/{url}.xml', '\Backend\Sitemap\Controllers\SitemapController@index');
