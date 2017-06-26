<?php
namespace Finetune\Finetune\Services\Gallery;

use Illuminate\Support\ServiceProvider;


class GalleryServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->bind('GalleryService', function($app)
        {
            return new GalleryService(
                $this->app->make('Finetune\Finetune\Repositories\Folders\FoldersInterface')
            );
        });
    }
}