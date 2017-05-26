<?php
namespace Finetune\Finetune\Services\Purifier;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;

class PurifierServiceServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->bind('PurifierService', function ($app) {
            return new PurifierService($app['config']);
        });
    }
    public function provides() {
        return ['PurifierService'];
    }
}