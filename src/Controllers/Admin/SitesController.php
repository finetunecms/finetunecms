<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Repositories\User\UserInterface;
use Finetune\Finetune\Controllers\BaseController;
use \Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session as Session;

class SitesController extends BaseController
{
    public $siteInterface;
    private $user;
    private $session;

    public function __construct(SiteInterface $siteInterface, Request $request, UserInterface $user, Session $session)
    {
        parent::__construct($siteInterface, $request);
        $this->siteInterface = $siteInterface;
        $this->user = $user;
        $this->session = $session;
    }

    public function index()
    {
        $site = $this->site;
        $route = $this->route;
        return view('finetune::sites.list', compact('site','route'));
    }

    public function show($id)
    {
        $foundSite = $this->siteInterface->find($id);
        $this->session->put('site', $foundSite);
        return redirect('/admin/content')->with(['message' => trans('site.message.selected'), 'class' => 'success']);
    }
}
