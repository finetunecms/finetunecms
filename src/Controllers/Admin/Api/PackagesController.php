<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\Packages\PackageInterface;
use \Illuminate\Http\Request;

class PackagesController extends BaseController
{
    protected $packages;

    public function __construct(SiteInterface $site, Request $request, PackageInterface $packages)
    {
        parent::__construct($site, $request);
        $this->packages = $packages;
    }

    public function index(){

    }

    public function show($packageArea){
        $packages = $this->packages->find($this->site, $packageArea);
        return response()->json($packages);
    }

    public function store(Request $request){

            $packageArea = $request->get('area');
            $node = $request->get('node');
            $packages = $this->packages->find($this->site, $packageArea, $node);
            return response()->json($packages);
    }
}