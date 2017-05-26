<?php

// Admin Interface Routes

Route::group(['middleware' => ['ability:Superadmin,can_administer_website'], 'namespace' => 'Finetune\Finetune\Controllers'], function () {
    Route::resource('sites', 'Admin\SitesController');

    Route::group(array('middleware' => 'hasSite'), function () {

        Route::group(['middleware' => ['ability:Superadmin,can_manage_media']], function () {
            Route::post('media/multidelete', 'Admin\MediaController@postMultiDelete');
            Route::post('media/move', 'Admin\MediaController@postMove');
            Route::post('media/{id}/crop', 'Admin\MediaController@crop');
            Route::post('media/folders', 'Admin\MediaController@postFolder');
            Route::PUT('media/folders/{id}', 'Admin\MediaController@postEditFolder');
            Route::DELETE('media/folder/{id}', 'Admin\MediaController@postDeleteFolder');
            Route::get('media/{tag}/order', 'Admin\MediaController@sort');
            Route::post('media/{tag}/order', 'Admin\MediaController@saveSort');
            Route::get('media/{id}/edit', 'Admin\MediaController@edit')->name('cropper');
            Route::resource('media', 'Admin\MediaController');
        });

        Route::get('content/{id}/create', 'Admin\ContentController@createChild')->name('content');
        Route::get('content/{id}/live', 'Admin\ContentController@livePreview');
        Route::resource('content', 'Admin\ContentController');


        Route::group(['middleware' => ['ability:Superadmin,can_manage_snippets']], function () {
            Route::resource('snippets/{group}/snippet', 'Admin\SnippetController');
            Route::resource('snippets', 'Admin\SnippetGroupController');
        });

        Route::group(['middleware' => ['ability:Superadmin,can_manage_types']], function () {
            Route::resource('types', 'Admin\TypeController');
        });
        Route::get('users/stop', 'Admin\UserController@stopImpersonate');
        Route::group(['middleware' => 'role:'.config('auth.superadminRole')], function(){
            Route::get('users/impersonate/{id}', 'Admin\UserController@impersonate');
        });
        Route::group(['middleware' => ['ability:Superadmin,can_manage_users']], function () {
            Route::resource('users', 'Admin\UserController');
        });

        Route::group(['middleware' => ['ability:Superadmin,can_manage_tags']], function () {
            Route::delete('tags/{nodeId}/{id}/delete', 'Admin\TaggingController@nodeDestroy');
            Route::resource('tags', 'Admin\TaggingController');
        });


        Route::get('preview', 'Admin\PreviewController@index');
        Route::put('preview/{id}', 'Admin\PreviewController@update');
        Route::get('preview/{any}', 'Admin\PreviewController@show')->where('any', '.*');

        //Route::resource('preview', 'Admin\PreviewController');
    });
});
