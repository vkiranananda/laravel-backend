<?php
	Route::get('upload/get-info/{id}', '\Backend\Root\Upload\Controllers\EditController@getInfo' );
	Route::put('upload/save-info/{id}', '\Backend\Root\Upload\Controllers\EditController@saveInfo' );

	// Route::get('upload/gallery/index/{id?}', '\Backend\Root\Upload\Controllers\UploadController@index');
	// Route::post('upload/gallery', '\Backend\Root\Upload\Controllers\UploadController@store');
	// Route::delete('upload/gallery/{id}', '\Backend\Root\Upload\Controllers\UploadController@destroy');