<?php

Route::get('filemanager', '\Backend\MediaFile\Controllers\FileManagerController@index')->name('file-manager.index');
Route::post('filemanager/upload/{id?}', '\Backend\MediaFile\Controllers\FileManagerController@store')->name('file-manager.upload');
Route::post('mediafile/upload/', '\Backend\MediaFile\Controllers\UploadController@store')->name('media-file.upload');
Route::get('filemanager/list/{id?}', '\Backend\MediaFile\Controllers\FileManagerController@list')->name('file-manager.list');
Route::delete('filemanager/upload/', '\Backend\MediaFile\Controllers\FileManagerController@destroy')->name('file-manager.destroy');
Route::post('filemanager/create-folder', '\Backend\MediaFile\Controllers\FileManagerController@createFolder')->name('file-manager.create-folder');
Route::post('filemanager/move', '\Backend\MediaFile\Controllers\FileManagerController@move')->name('file-manager.move');
Route::post('filemanager/copy', '\Backend\MediaFile\Controllers\FileManagerController@copy')->name('file-manager.copy');
