<?php
namespace Finetune\Finetune\Services\Purifier;

use \Illuminate\Support\Facades\Facade;

class PurifierFacade extends Facade {

    protected static function getFacadeAccessor(){
        return 'PurifierService';
    }
}