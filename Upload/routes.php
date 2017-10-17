<?php
	Route::get('upload/get-info/{id}', '\Backend\Root\Upload\Controllers\EditController@getInfo');
	Route::put('upload/save-info/{id}', '\Backend\Root\Upload\Controllers\EditController@saveInfo');

//	Route::get('upload/test', '\Backend\Root\Upload\Controllers\EditController@test');
