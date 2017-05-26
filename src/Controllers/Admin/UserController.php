<?php
namespace Finetune\Finetune\Controllers\Admin;

use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\User\UserInterface;
use Illuminate\Foundation\Application;
use \Illuminate\Http\Request;
use \Entrust;

class UserController extends BaseController
{
    private $siteInterface;
    private $user;
    private $app;

    public function __construct(SiteInterface $siteInterface, Request $request, UserInterface $user, Application $application)
    {
        parent::__construct($siteInterface, $request);
        $this->siteInterface = $siteInterface;
        $this->user = $user;
        $this->app = $application;
    }

    public function index()
    {
        $site = $this->site;
        $route = $this->route;
        $sites = $this->siteInterface->all()->toJson();
        return view('finetune::user.list', compact('site','route','sites'));
    }

    public function impersonate($id, Request $request)
    {
        if ($id == $request->user()->getKey()) {
            $request->session()->flash('error','Can\'t Impersonate yourself.');
            return redirect()->back();
        }else{
            if(Entrust::hasRole(config('auth.superadminRole'))){
                $impersonator = $this->user->find($id);
                $isAdmin = false;
                foreach($impersonator->roles as $role){
                    if($role->name == config('auth.superadminRole')){
                        $isAdmin = true;
                    }
                }
                if(!$isAdmin) {
                    auth()->user()->impersonate($impersonator);
                    return redirect()->to('/admin/content');
                }
                else
                {
                    $request->session()->flash('error','Impersonate disabled for this user.');
                    return redirect()->back();
                }
            }else{
                $request->session()->flash('error', 'Impersonate disabled for this user.');
                return redirect()->back();
            }
        }
    }

    public function stopImpersonate(Request $request)
    {
        auth()->user()->leaveImpersonation();
        $request->session()->flash('success','Welcome back!');
        return redirect()->back();
    }

}