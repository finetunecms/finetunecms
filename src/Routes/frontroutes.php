<?php

$api = [];

$web = [
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class
];
Route::group(['middleware' => $api, 'prefix' => 'api'], function () {
    require ('api.php');
});
Route::group(['middleware' => $web,], function () {
    require ('web.php');
});
