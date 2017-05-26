<?php
namespace Finetune\Finetune\Controllers;

use Finetune\Finetune\Repositories\Media\MediaInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use \Illuminate\Http\Request;
use \Illuminate\Support\Facades\File;
use \Intervention\Image\ImageManagerStatic as Image;
use \Illuminate\Contracts\Filesystem\Filesystem;

class MediaController extends BaseController
{
    private $media;
    private $request;
    private $fileContract;

    public function __construct(MediaInterface $media, Request $request, SiteInterface $site, Filesystem $filesystem)
    {
        parent::__construct($site, $request);
        $this->media = $media;
        $this->request = $request;
        $this->fileContract = $filesystem;
    }

    public function image($folder, $image, $width = 0)
    {
        $img = $this->media->renderImage($this->site, $image);
        if($img == "Image Not Found"){
            return null;
        }
        $name = $img->filename;
        if (!empty($width)) {
            $widths = explode('x', $width);
            $name = $name . '-' . $widths[0];
            if (isset($widths[1])) {
                $name = $name . 'x' . $widths[1];
                $fit = !empty($this->request->get('fit'));
                if($fit){
                    $name = $name . '-fit';
                    $bg = empty($this->request->get('bg')) ? '000000' : $this->request->get('bg');
                    $name = $name . '-'.$bg;
                    $canvas = Image::canvas($widths[0], $widths[1], '#'.$bg);
                    $img->resize($widths[0], $widths[1], function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $canvas->insert($img, 'center');
                    $canvas->extension = $img->extension;
                    $img = $canvas;
                }else{
                    $img = $this->media->fit($img, $widths[0], $widths[1]);
                }
            } else {
                $img = $this->media->resize($img, $width);
            }
        }
        if ($this->request->has('c')) {
            $name = $name . '-c';
            $crop = $this->request->get('c');
            $cropSettings = explode('-', $crop);
            if (isset($cropSettings[0]) && isset($cropSettings[1])) {
                $name = $name.'-'. $cropSettings[0].'-'. $cropSettings[1];
                if (isset($cropSettings[2]) && isset($cropSettings[3])) {
                    $img = $this->media->crop($img, $cropSettings[0], $cropSettings[1], $cropSettings[2], $cropSettings[3]);
                } else {
                    $img = $this->media->crop($img, $cropSettings[0], $cropSettings[1]);
                }
            }
        }
        if ($this->request->has('f')) {
            $name = $name . '-' . $this->request->get('f');
            $img = $this->media->flip($img, $this->request->get('f'));
        }
        if ($this->request->has('rot')) {
            $name = $name . '-' . $this->request->get('rot');
            $img = $this->media->rotate($img, $this->request->get('rot'));
        }
        if($this->request->has('greyscale')){
            $name = $name.'-grey';
            $img = $this->media->greyscale($img);
        }
        if (!$this->request->has('nosave')) {
            if($this->request->has('q')){
                $name = $name.'-'.$this->request->get('q');
                $name = $name.'.'.$img->extension;
                $this->media->saveRender($this->site, $name, $img, $this->request->get('q'));
            }else{
                $name = $name.'.'.$img->extension;
                $this->media->saveRender($this->site, $name, $img, 100);
            }
        }
        $response = $img->response($img->extension, $this->request->has('q') ?  $this->request->get('q') : 100);
        $response->header('Content-Type', $img->mime);
        return $response;
    }

    public function file($folder,$fileName)
    {
        $file = $this->media->findByFileName($this->site, $fileName);
        if(!empty($file)){
            if ($this->fileContract->exists($file->path)) {
                $content = $this->fileContract->get($file->path);
                $response = response($content);
                $response->header('Content-Type', $file->mime);
                return $response;
            }
        }
        return "No File Found";
    }
}