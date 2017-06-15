<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use \Illuminate\Http\Request as NormalRequest;
use Finetune\Finetune\Requests\Site\SiteRequest;
use \Illuminate\Translation\Translator;
use \Entrust;


class SitesController extends BaseController
{

    protected $siteInterface;
    protected $lang;

    public function __construct(SiteInterface $siteInterface, NormalRequest $request,  Translator $lang)
    {
        parent::__construct($siteInterface, $request);
        $this->siteInterface = $siteInterface;
        $this->lang = $lang;
    }

    public function index(){
        if (Entrust::hasRole(config('auth.superadminRole'))) {
            return response()->json($this->siteInterface->all(), 200);
        } else{
            return response()->json(auth()->user()->sites()->get(), 200);
        }
    }

    public function show($id){
        if (Entrust::hasRole(config('auth.superadminRole'))) {
            return response()->json($this->siteInterface->find($id), 200);
        } else{
            $site = auth()->user()->sites()->find($id);
            return response()->json($site, 200);
        }
    }

    public function store(SiteRequest $request)
    {
        if (Entrust::ability([config('auth.superadminRole')], ['can_manage_sites'])) {
            $sites = $this->siteInterface->create($request->except('_token'));
            $array = [
                'sites' => $sites->toArray(),
                'alertType' => 'success',
                'alertMessage' => $this->lang->trans('finetune::sites.notifications.created')
            ];
            return response()->json($array, 200);
        }else{
            return response()->json(['No Permissions for managing sites'], 403);
        }
    }

    public function update($id, SiteRequest $request)
    {
        $userSites = auth()->user()->sites()->get();
        $allowedSite = false;
        foreach($userSites as $site){
            if($site->id == $id){
                $allowedSite = true;
            }
        }
        if($allowedSite ){
            $sites = $this->siteInterface->update($id, $request->except('_token'));
            $array = [
                'sites'=> $sites->toArray(),
                'alertType'=> 'success',
                'alertMessage' => $this->lang->trans('finetune::sites.notifications.updated')
            ];
            return response()->json($array, 200);
        }else{
            $array = [
                'sites'=> $userSites->toArray(),
                'alertType'=> 'success',
                'alertMessage' => $this->lang->trans('finetune::sites.notifications.notallowed')
            ];
            return response()->json($array, 200);
        }

    }

    public function destroy(SiteRequest $request)
    {
        $sites = $request->get('sites');
        foreach ($sites as $site) {
            $this->siteInterface->delete($site['id']);
        }
        $sites = $this->siteInterface->all();
        $array = [
            'types'=> $sites->toArray(),
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::sites.notifications.deleted')
        ];
        return Response()->json($array, 200);
    }
}