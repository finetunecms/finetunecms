<?php
namespace Finetune\Finetune\Controllers;

use Finetune\Finetune\Repositories\Site\SiteInterface;
use \App\Http\Controllers\Controller;
use \Illuminate\Http\Request;

class BaseController extends Controller
{
    public $site;
    public $route;

    public function __construct(SiteInterface $siteInterface, Request $request)
    {
        $this->route = explode('.', $request->route()->getName())[0];
        $this->site = $siteInterface->getSite($request);
    }
}