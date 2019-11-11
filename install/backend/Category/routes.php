<?php
	Route::resource('cat', '\Backend\Category\Controllers\CategoryController');
	Route::resource('cat-root', '\Backend\Category\Controllers\CategoryRootController');
	Route::get('sortable', '\Backend\Category\Controllers\CategoryController@listSortable');
	Route::put('sortable', '\Backend\Category\Controllers\CategoryController@listSortableSave');
	Route::get('sortable-root', '\Backend\Category\Controllers\CategoryRootController@listSortable');
	Route::put('sortable-root', '\Backend\Category\Controllers\CategoryRootController@listSortableSave');

	Route::get('routes', '\Backend\Category\Controllers\InfoController@routes');
