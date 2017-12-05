<?php
	Route::get('upload/get-info/{id}', '\Backend\Root\Upload\Controllers\EditController@getInfo');
	Route::put('upload/save-info', '\Backend\Root\Upload\Controllers\EditController@saveInfo');
