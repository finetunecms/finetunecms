<?php

namespace Finetune\Finetune\Services\Gallery;

use Finetune\Finetune\Repositories\Folders\FoldersInterface;

class GalleryService
{
    protected $folders;

    public function __construct(FoldersInterface $folders )
    {
        $this->folders = $folders;
    }

    public function getGallery($site, $tag){
        $folders = $this->folders->all($site);
        $images = [];
        foreach($folders as $folder){
            if($folder->tag == $tag){
                $images = $folder->media()->orderBy('order')->get();
            }
        }
        return $images;
    }

    public function insertGallery($site, $gallery){
        return $this->renderGallery($site, $gallery);
    }
    public function renderGallery($site, $gallery){
        $gallery = explode(',', $gallery);
        $gallery[0] = trim($gallery[0]);
        if (!empty($gallery[0])) {
            $galleryObj = $this->getGallery($site, $gallery[0]);
        } else {
            $galleryObj = '';
        }
        $view ='';
        if (!empty($galleryObj)) {
            $this->view = app('view');
            if (isset($gallery[1])) {
                $gallery[1] = trim($gallery[1]);
                if ($this->view->exists($site->theme . '::galleries.' . $gallery[1] . '-' . $gallery[0])) {
                    $view = $this->view->make($site->theme . "::galleries." . $gallery[1] . '-' . $gallery[0], ['images' => $galleryObj])->render();
                } else {
                    if ($this->view->exists($site->theme . '::galleries.' . $gallery[1] . '-defaultGallery')) {
                        $view = $this->view->make($site->theme . '::galleries.' . $gallery[1] . '-defaultGallery', ['images' => $galleryObj])->render();
                    } else {
                        $view = 'Gallery View File Not found';
                    }
                }
            } else {
                if ($this->view->exists($site->theme . '::galleries.' . $gallery[0])) {
                    $view = $this->view->make($site->theme . "::galleries." . $gallery[0], ['images' => $galleryObj])->render();
                } else {
                    if ($this->view->exists($site->theme . '::galleries.defaultGallery')) {
                        $view = $this->view->make($site->theme . "::galleries.defaultGallery", ['images' => $galleryObj])->render();
                    } else {
                        $view = 'Gallery View File Not found';
                    }
                }
            }
        }
        return $view;
    }


}