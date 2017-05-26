<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\Tagging\TaggingInterface;
use \Illuminate\Http\Request;

class TaggingController extends BaseController
{
    private $tagging;

    public function __construct(SiteInterface $site, Request $request,TaggingInterface $tagging)
    {
        parent::__construct($site, $request);
        $this->tagging = $tagging;
    }

    public function index()
    {
        $route = $this->route;
        $site = $this->site;
        $packages = config('packages.tagging-list');
        return view('finetune::tags.list', compact('route','site','packages'));
    }

    public function show($id)
    {
        $route = $this->route;
        $site = $this->site;
        $packages = config('packages.tagging-list');
        $tag = $this->tagging->find($id);
        if(empty($tag)){
            abort('404');
        }
        return view('finetune::tags.show', compact('route','site','tag','packages'));
    }
}
