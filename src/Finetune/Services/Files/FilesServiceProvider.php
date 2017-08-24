<?php
namespace Finetune\Finetune\Services\Files;

use Illuminate\Support\ServiceProvider;


class FilesServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->bind('FilesService', function($app)
        {
            return new FilesService(
                $this->app->make('Finetune\Finetune\Repositories\Folders\FoldersInterface')
            );
        });
    }
}