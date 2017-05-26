<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\Media\MediaInterface;
use \Illuminate\Http\Request;

/**
 * Class MediaController
 * @package admin
 */
class MediaController extends BaseController
{
    /**
     * @var MediaInterface
     */
    private $media;

    /**
     * @param MediaInterface $media
     */
    public function __construct(SiteInterface $site, Request $request, MediaInterface $media)
    {
        parent::__construct($site, $request);
        $this->media = $media;
    }

    public function index()
    {
        $packages = config('packages.media-list');
        $path = '/admin/media';
        $route = $this->route;
        $site = $this->site;
        $folders = [];
        return view('finetune::media.list', compact('site', 'route','path', 'folders', 'packages'));
    }


    public function show($tag)
    {
        $packages = config('packages.media-list');
        $path = '/admin/media';
        $route = $this->route;
        $site = $this->site;
        $folder = [];
        return view('finetune::media.show', compact('site', 'route','path', 'packages', 'folder'));
    }

    public function edit($id)
    {
        $route = $this->route;
        $site = $this->site;
        $media = $this->media->find($id);
        if($media->type == 'image'){
            return view('finetune::media.crop', compact('site', 'route','path','media'));
        }else{
            return view('finetune::media.update', compact('site', 'route','path','media'));
        }
    }
    public function crop($id, Request $request){
        $this->media->doCrop($this->site, $id, $request->except('_token'));
        return redirect('/admin/media');
    }

}
