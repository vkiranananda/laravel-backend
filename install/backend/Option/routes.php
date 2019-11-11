<?php

Route::resource('options', '\Backend\Option\Controllers\OptionController');
Route::get('general-config', '\Backend\Option\Controllers\OptionGeneralController@edit');
Route::put('general-config/{id}/update', '\Backend\Option\Controllers\OptionGeneralController@update');