<?php
namespace Finetune\Finetune\Services\Node;

use Illuminate\Support\ServiceProvider;

class NodeServiceServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->bind('nodeService', function($app)
        {
           return new NodeService(
             $app->make('Finetune\Finetune\Repositories\Node\NodeInterface')
           );
        });
    }
}