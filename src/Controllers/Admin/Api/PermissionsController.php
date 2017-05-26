<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Permissions\PermissionsInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Requests\Permissions\PermissionsRequest;
use \Illuminate\Http\Request;
use \Illuminate\Translation\Translator;

class PermissionsController extends BaseController
{
    protected $permissions;
    protected $lang;

    public function __construct(SiteInterface $site, Request $request, PermissionsInterface $permissions, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->permissions = $permissions;
        $this->lang = $lang;
    }

    public function index()
    {
        return response()->json($this->permissions->all(), 200);
    }

    public function show($id){
        return response()->json($this->permissions->find($id), 200);
    }

    public function store(PermissionsRequest $request)
    {
        $permissions = $this->permissions->create($request->except('_token'));
        $array = [
            'permissions'=> $permissions->toArray(),
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::permissions.notifications.created')
        ];
        return Response()->json($array, 200);
    }

    public function update($id, PermissionsRequest $request)
    {
        $permissions = $this->permissions->update($id, $request->except('_token'));
        $array = [
            'permissions'=> $permissions->toArray(),
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::permissions.notifications.updated')
        ];
        return Response()->json($array, 200);
    }

    public function destroy(PermissionsRequest $request)
    {
        $permissions = $request->get('permissions');
        foreach ($permissions as $permission) {
            $this->permissions->delete($permission['id']);
        }
        $permissions = $this->permissions->all();
        $array = [
            'permissions'=> $permissions->toArray(),
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::permissions.notifications.updated')
        ];
        return Response()->json($array, 200);
    }
}
