<?php

Route::get('/filemanager', '\Backend\FileManager\Controllers\FileManagerController@index')->name('filemanager');
// Route::get($modUrl . '/upload/index/{id?}', '\\Backend\\' . $mod . '\\Controllers\\' . $controller . 'Controller@index');
Route::post('filemanager/upload/{id?}', '\Backend\FileManager\Controllers\FileManagerController@store')->name('filemanager.upload');
Route::get('filemanager/list/{id?}', '\Backend\FileManager\Controllers\FileManagerController@list')->name('filemanager.list');
Route::get('filemanager/file/{id}', '\Backend\FileManager\Controllers\FileManagerController@getFile')->name('filemanager.get-file');
Route::delete('filemanager/upload/', '\Backend\FileManager\Controllers\FileManagerController@destroy')->name('filemanager.destroy');
Route::post('filemanager/create-folder', '\Backend\FileManager\Controllers\FileManagerController@createFolder')->name('filemanager.create-folder');
Route::post('filemanager/move', '\Backend\FileManager\Controllers\FileManagerController@move')->name('filemanager.move');
Route::post('filemanager/copy', '\Backend\FileManager\Controllers\FileManagerController@copy')->name('filemanager.copy');
// Route::get($modUrl . '/upload/edit/{id?}', '\\Backend\\' . $mod . '\\Controllers\\' . $controller . 'Controller@edit');
// Route::put($modUrl . '/upload/update/{id?}', '\\Backend\\' . $mod . '\\Controllers\\' . $controller . 'Controller@update');
