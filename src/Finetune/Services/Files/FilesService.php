<?php

namespace Finetune\Finetune\Services\Files;

use Finetune\Finetune\Repositories\Folders\FoldersInterface;

class FilesService
{
    protected $folders;

    public function __construct(FoldersInterface $folders)
    {
        $this->folders = $folders;
    }

    public function getFileBank($site, $tag){
        $folders = $this->folders->all($site);
        $files = [];
        foreach($folders as $folder){
            if($folder->tag == $tag){
                $files = $folder->media()->orderBy('order')->where('type', 'file')->get();
            }
        }
        return $files;
    }

    public function renderFileBank($site, $folder){
        $fileBank = trim($folder);
        if (!empty( $fileBank)) {
            $fileBankObj = $this->getFileBank($site, $fileBank);
        } else {
            $fileBankObj = '';
        }
        $view ='';
        if (!empty($fileBankObj)) {
            $this->view = app('view');
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