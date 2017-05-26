<?php
namespace Finetune\Finetune\Services\Helper;

use Illuminate\Support\ServiceProvider;


class HelperServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->bind('HelperService', function($app)
        {
           return new HelperService();
        });
    }
}