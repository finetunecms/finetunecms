<?php

namespace Finetune\Finetune\Services\Media;

use Finetune\Finetune\Repositories\Media\MediaInterface;
use \Illuminate\Routing\Router;

class MediaService
{
    protected $media;

    public function __construct(MediaInterface $media)
    {
        $this->media = $media;
    }

    public function getByType($site, $type){
        $media = $this->media->all($site);
        $items = [];
        foreach($media as $item){
            if($media->type == $type){
                $items[] = $item;
            }
        }
        return collect($items);
    }

    public function getList($site, $type, $column){
        $items = $this->getByType($site, $type);
        return $items->pluck($column);
    }
}