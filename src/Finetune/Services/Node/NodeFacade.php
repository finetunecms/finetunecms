<?php

namespace Finetune\Finetune\Services\Node;

use \Illuminate\Support\Facades\Facade;

class NodeFacade extends Facade {

    protected static function getFacadeAccessor(){
        return 'nodeService';
    }
}