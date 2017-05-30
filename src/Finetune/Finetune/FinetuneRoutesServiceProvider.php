<?php
namespace Finetune\Finetune;

use Illuminate\Support\ServiceProvider;

class FinetuneRoutesServiceProvider extends ServiceProvider{

    protected $path = __DIR__.'/../..';


    public function register()
    {
    }

    public function boot()
    {
        $this->loadRoutesFrom($this->path.'/Routes/frontroutes.php');
    }
}