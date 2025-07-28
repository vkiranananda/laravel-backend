<?php

Route::get('/filemanager', '\Backend\FileManager\Controllers\FileManagerController@index')->name('file-manager.index');
Route::post('filemanager/upload/{id?}', '\Backend\FileManager\Controllers\FileManagerController@store')->name('file-manager.upload');
Route::get('filemanager/list/{id?}', '\Backend\FileManager\Controllers\FileManagerController@list')->name('file-manager.list');
Route::get('filemanager/file/{id}', '\Backend\FileManager\Controllers\FileManagerController@getFile')->name('file-manager.get-file');
Route::delete('filemanager/upload/', '\Backend\FileManager\Controllers\FileManagerController@destroy')->name('file-manager.destroy');
Route::post('filemanager/create-folder', '\Backend\FileManager\Controllers\FileManagerController@createFolder')->name('file-manager.create-folder');
Route::post('filemanager/move', '\Backend\FileManager\Controllers\FileManagerController@move')->name('file-manager.move');
Route::post('filemanager/copy', '\Backend\FileManager\Controllers\FileManagerController@copy')->name('file-manager.copy');
