<?php

namespace Finetune\Finetune\Services\Media;

use \Illuminate\Support\Facades\Facade;

class MediaFacade extends Facade {

    protected static function getFacadeAccessor(){
        return 'MediaService';
    }
}