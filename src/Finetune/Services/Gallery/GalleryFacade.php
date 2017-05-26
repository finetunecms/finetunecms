<?php

namespace Finetune\Finetune\Services\Gallery;

use \Illuminate\Support\Facades\Facade;

class GalleryFacade extends Facade {

    protected static function getFacadeAccessor(){
        return 'GalleryService';
    }
}