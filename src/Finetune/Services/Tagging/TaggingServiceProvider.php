<?php
namespace Finetune\Finetune\Services\Tagging;

use Illuminate\Support\ServiceProvider;


/**
 * Class TaggingServiceServiceProvider
 * @package Services\Snippet
 */
class TaggingServiceProvider extends ServiceProvider
{
    /**
     * Register the Tagging provider
     */
    public function register(){
        $this->app->bind('TaggingService', function($app)
        {
           return new TaggingService(
             $app->make('Finetune\Finetune\Repositories\Tagging\TaggingInterface')
           );
        });
    }
}