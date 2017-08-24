<?php
// Admin Api

/*
Route::get('api/type/{id}', 'Admin\ApiController@type');
Route::get('api/live/{id}', 'Admin\ApiController@live');
Route::post('api/live', 'Admin\ApiController@livePost');

Route::get('api/node/{id}', 'Admin\ApiController@node');
Route::get('api/media', 'Admin\ApiController@media');

Route::get('api/media/folder/{tag}', 'Admin\ApiController@mediaFolder');
Route::get('api/media/folder/{tag}/{type}', 'Admin\ApiController@mediaFolder');
Route::get('api/media/{id}', 'Admin\ApiController@media');
Route::get('api/folders', 'Admin\ApiController@folders');
Route::get('api/folders/{id}', 'Admin\ApiController@folders');

Route::get('api/fields/{id}', 'Admin\ApiController@fields');
Route::get('api/small/{small}', 'Admin\ApiController@smallMenu');
Route::delete('api/media/{id}', 'Admin\ApiController@mediaDestroy');
Route::post('api/mediasearch', 'Admin\ApiController@mediaSearch');
Route::post('api/mediasearch/{folder}', 'Admin\ApiController@mediaSearch');
Route::post('media/upload', 'Admin\MediaController@store');
Route::get('api/galleries', 'Admin\ApiController@galleries');
*/

Route::group(['middleware' => ['ability:Superadmin,can_administer_website'], 'namespace' => 'Finetune\Finetune\Controllers'], function() {
    Route::group(['middleware' => ['ability:Superadmin,can_manage_users']], function() {
        Route::resource('users', 'Admin\Api\UserController');
        Route::resource('roles', 'Admin\Api\RolesController');
        Route::resource('permissions', 'Admin\Api\PermissionsController');
    });

        Route::resource('fields', 'Admin\Api\FieldsController');
        Route::resource('types', 'Admin\Api\TypeController');


        Route::resource('preview', 'Admin\Api\PreviewController');

        Route::delete('blocks/{nodeid}/{id}', 'Admin\Api\NodeController@destroyOrphan');
        Route::post('nodes/publish', 'Admin\Api\NodeController@publish');
        Route::post('nodes/search', 'Admin\Api\NodeController@search');
        Route::post('nodes/order', 'Admin\Api\NodeController@saveOrder');
        Route::post('nodes/move', 'Admin\Api\NodeController@move');
        Route::post('nodes/links', 'Admin\Api\NodeController@links');
        Route::resource('nodes', 'Admin\Api\NodeController');


        Route::post('media/move', 'Admin\Api\MediaController@move');
        Route::post('media/order', 'Admin\Api\MediaController@order');
        Route::get('media/options', 'Admin\Api\MediaController@getMediaOptions');
        Route::resource('media', 'Admin\Api\MediaController');
        Route::resource('folders', 'Admin\Api\FolderController');


        Route::resource('tags', 'Admin\Api\TaggingController');

        Route::resource('snippetgroups', 'Admin\Api\SnippetGroupController');
        Route::post('snippets/publish', 'Admin\Api\SnippetController@publish');
        Route::post('snippets/order', 'Admin\Api\SnippetController@saveOrder');
        Route::resource('snippets', 'Admin\Api\SnippetController');


    Route::resource('sites', 'Admin\Api\SitesController');
    Route::resource('packages', 'Admin\Api\PackagesController');


});

