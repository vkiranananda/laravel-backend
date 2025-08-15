<?php

// if (app()->runningInConsole()) return;

Route::get('uploads/{id?}', '\Backend\MediaFile\Controllers\UploadController@getFile')->name('uploads.get-file');

Route::group(['prefix' => 'content', 'middleware' => ['auth.basic']], function () {
    Route::get('/', '\Backend\Home\Controllers\HomeController@index');
    Route::get('/icons', function () {
        return view('Backend::icons');
    })->name('icons');
    Backend::installRoutes('Category', ['module']);
    Backend::installRoutes('News');
    Backend::installRoutes('Page');
    Backend::installRoutes('User', ['module']);
    Backend::installRoutes('Option');
    Backend::installRoutes('MenuBuilder');
    Backend::installRoutes('MediaFile', ['module']);
});
