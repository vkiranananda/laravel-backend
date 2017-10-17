<?php
	Route::get('cat/tree-expand/{id}/{status}', '\Backend\Root\Category\Controllers\CategoryController@treeExpand')->where(['id' => '[0-9]+', 'status' => '1|0']);
	Route::resource('cat', '\Backend\Root\Category\Controllers\CategoryController');
	Route::resource('cat-root', '\Backend\Root\Category\Controllers\CategoryRootController');
	Route::get('cart', '\Backend\Root\Category\Controllers\CartController@index');