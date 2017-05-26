<?php

namespace Finetune\Finetune\Services\Files;

use \Illuminate\Support\Facades\Facade;

class FilesFacade extends Facade {

    protected static function getFacadeAccessor(){
        return 'FilesService';
    }
}