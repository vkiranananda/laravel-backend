<?php

Route::resource('options', '\Backend\Root\Option\Controllers\OptionController');
Route::get('general-config', '\Backend\Root\Option\Controllers\OptionGeneralController@edit');
Route::put('general-config/update', '\Backend\Root\Option\Controllers\OptionGeneralController@update');