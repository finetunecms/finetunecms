<?php

namespace Finetune\Finetune\Services\Snippet;

use \Illuminate\Support\Facades\Facade;

/**
 * Class SnippetFacade
 * @package Services\Snippet
 */
class SnippetFacade extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'SnippetService';
    }
}