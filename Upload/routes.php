<?php
	Route::get('upload/get-info/{id}', '\Backend\Upload\Controllers\EditController@getInfo');
	Route::put('upload/save-info/{id}', '\Backend\Upload\Controllers\EditController@saveInfo');

//	Route::get('upload/test', '\Backend\Upload\Controllers\EditController@test');
