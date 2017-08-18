<?php
namespace Finetune\Finetune\Services\Media;

use Illuminate\Support\ServiceProvider;


class MediaServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->bind('MediaService', function($app)
        {
           return new MediaService();
        });
    }
}