<?php

// if (app()->runningInConsole()) return;

Route::get('uploads/{id}', '\Backend\MediaFile\Controllers\UploadController@getFile')->name('uploads.get-file');

Route::group(['prefix' => 'content', 'middleware' => ['auth.basic']], function () {
    Route::get('/', '\Backend\Home\Controllers\HomeController@index');
    Route::get('/icons', function () {
        return view('Backend::icons');
    })->name('icons');
    Backend::installRoutes('Category', ['upload', 'module']);
    Backend::installRoutes('News', ['upload']);
    Backend::installRoutes('Page', ['upload']);
    Backend::installRoutes('User', ['module']);
    Backend::installRoutes('Option', ['upload']);
    Backend::installRoutes('MenuBuilder');
    Backend::installRoutes('MediaFile', ['module']);
});
                                            