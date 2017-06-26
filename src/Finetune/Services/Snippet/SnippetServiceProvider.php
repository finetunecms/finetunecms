<?php
namespace Finetune\Finetune\Services\Snippet;

use Illuminate\Support\ServiceProvider;


/**
 * Class SnippetServiceServiceProvider
 * @package Services\Snippet
 */
class SnippetServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider
     */
    public function register(){
        $this->app->bind('SnippetService', function($app)
        {
           return new SnippetService(
             $app->make('Finetune\Finetune\Repositories\Snippet\SnippetInterface'),
             $app->make('Finetune\Finetune\Repositories\SnippetGroup\SnippetGroupInterface')
           );
        });
    }
}