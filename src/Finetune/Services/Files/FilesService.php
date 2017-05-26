<?php

namespace Finetune\Finetune\Services\Files;

use Finetune\Finetune\Repositories\Folders\FoldersInterface;
use \Illuminate\Contracts\View\Factory as View;
class FilesService
{
    protected $folders;
    protected $view;

    public function __construct(FoldersInterface $folders, View $view)
    {
        $this->folders = $folders;
        $this->view = $view;
    }

    public function getFileBank($site, $tag){
        $folders = $this->folders->all($site);
        $images = [];
        foreach($folders as $folder){
            if($folder->tag == $tag){
                $images = $folder->media()->orderBy('order')->get();
            }
        }
        return $images;
    }

    public function insertFileBank($site, $folder){
        echo $this->renderFileBank($site, $folder);
    }

    public function renderFileBank($site, $folder){
        $fileBank = trim($folder);
        if (!empty( $fileBank)) {
            $fileBankObj = $this->getFileBank($site, $fileBank);
        } else {
            $fileBankObj = '';
        }
        $view ='';
        if (!empty($galleryObj)) {
                if ($this->view->exists($site->theme . '::filebanks.' .  $fileBank)) {
                    $view = $this->view->make($site->theme . "::filebanks." .  $fileBank, ['files' => $fileBankObj])->render();
                } else {
                    if ($this->view->exists($site->theme . '::filebanks.defaultFileBank')) {
                        $view = $this->view->make($site->theme . "::filebanks.defaultFileBank", ['files' => $fileBankObj])->render();
                    } else {
                        $view = 'filebank View File Not found';
                    }
                }
        }
        return $view;
    }


}