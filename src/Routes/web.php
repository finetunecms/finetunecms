<?php
// Admin Login Routes

Route::group(['middleware' => ['Restrict']], function () {
    Route::get('/auth/login', '\Finetune\Finetune\Controllers\AuthController@index');
    Route::get('/auth/logout', array(
        'uses' => '\Finetune\Finetune\Controllers\AuthController@destroy',
        'as' => 'auth.destroy'
    ));
    Route::get('admin', '\Finetune\Finetune\Controllers\AuthController@index');

    Route::post('/auth/password', '\Finetune\Finetune\Controllers\AuthController@postReset');
    Route::post('/auth/passwordchange', '\Finetune\Finetune\Controllers\AuthController@postPasswordChange');
    Route::get('/auth/reset/{key}', '\Finetune\Finetune\Controllers\AuthController@getReset');
    Route::resource('auth', '\Finetune\Finetune\Controllers\AuthController');
});

// Frontend Routes

Route::any('search', '\Finetune\Finetune\Controllers\PublicController@search');
Route::any('sitemap', '\Finetune\Finetune\Controllers\PublicController@sitemap');
Route::get('/image/{folder}/{image}/{width}', '\Finetune\Finetune\Controllers\MediaController@image');
Route::get('/image/{folder}/{image}', '\Finetune\Finetune\Controllers\MediaController@image');
Route::get('/files/{folder}/{filename}', '\Finetune\Finetune\Controllers\MediaController@file');
Route::get('/file/{folder}/{filename}', '\Finetune\Finetune\Controllers\MediaController@file');
Route::any('/form/{form}', '\Finetune\Finetune\Controllers\PublicController@email');

Route::get('/test/styles', '\Finetune\Finetune\Controllers\PublicController@testStyles');

Route::any('{any}', '\Finetune\Finetune\Controllers\PublicController@index')->where('any', '.*');