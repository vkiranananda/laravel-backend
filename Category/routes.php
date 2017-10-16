<?php
	Route::get('cat/tree-expand/{id}/{status}', '\Backend\Category\Controllers\CategoryController@treeExpand')->where(['id' => '[0-9]+', 'status' => '1|0']);
	Route::resource('cat', '\Backend\Category\Controllers\CategoryController');
	Route::resource('cat-root', '\Backend\Category\Controllers\CategoryRootController');
	Route::get('cart', '\Backend\Category\Controllers\CartController@index');