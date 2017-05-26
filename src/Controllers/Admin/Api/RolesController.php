<?php
namespace Finetune\Finetune\Controllers\Admin\Api;

use Finetune\Finetune\Controllers\BaseController;
use Finetune\Finetune\Repositories\Roles\RolesInterface;
use Finetune\Finetune\Repositories\Site\SiteInterface;
use Finetune\Finetune\Requests\Role\RoleRequest;
use \Illuminate\Http\Request;
use \Illuminate\Translation\Translator;

class RolesController extends BaseController
{
    protected $roles;
    protected $lang;

    public function __construct(SiteInterface $site, Request $request, RolesInterface $roles, Translator $lang)
    {
        parent::__construct($site, $request);
        $this->roles = $roles;
        $this->lang = $lang;

    }

    public function index()
    {
        return response()->json($this->roles->all(true), 200);
    }

    public function show($id){
        return response()->json($this->roles->find($id), 200);
    }

    public function store(RoleRequest $request)
    {
        $roles = $this->roles->create($request->except('_token'));
        $array = [
            'roles'=> $roles,
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::roles.notifications.created')
        ];
        return Response()->json($array, 200);
    }

    public function update($id, RoleRequest $request)
    {
        $roles = $this->roles->update($id, $request->except('_token'));
        $array = [
            'roles'=> $roles,
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::roles.notifications.updated')
        ];
        return Response()->json($array, 200);
    }

    public function destroy(RoleRequest $request)
    {
        $roles = $request->get('roles');
        foreach ($roles as $role) {
            $this->roles->delete($role['id']);
        }
        $roles = $this->roles->all();
        $array = [
            'roles'=> $roles,
            'alertType'=> 'success',
            'alertMessage' => $this->lang->trans('finetune::roles.notifications.updated')
        ];
        return Response()->json($array, 200);
    }
}
