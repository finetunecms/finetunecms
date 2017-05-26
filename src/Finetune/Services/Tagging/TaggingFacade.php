<?php

namespace Finetune\Finetune\Services\Tagging;

use \Illuminate\Support\Facades\Facade;

/**
 * Class TaggingFacade
 * @package Services\Snippet
 */
class TaggingFacade extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'TaggingService';
    }
}