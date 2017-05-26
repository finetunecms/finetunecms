<?php
namespace Finetune\Finetune\Repositories\Permissions;

use Finetune\Finetune\Entities\Permission;
use \Site;
use \Auth;
use \Packages;
use Illuminate\Support\Facades\Cache as Cache;

class PermissionsRepository implements PermissionsInterface
{

    public function all()
    {
        return Permission::where('usable', '=', 1)->whereNull('deleted_at')->with('roles')->get();
    }


    public function find($id)
    {
        return Permission::with('roles')->whereNull('deleted_at')->find($id);
    }

    public function create($request)
    {
        $permissions = new Permission();
        $permissions->name = $request['name'];
        $permissions->display_name = $request['display_name'];
        $permissions->save();
        return $this->all();
    }

    public function update($id, $request)
    {
        $permissions = $this->find($id);
        $permissions->name = $request['name'];
        $permissions->display_name = $request['display_name'];
        $permissions->save();
        return $this->all();
    }

    public function delete($id)
    {
        Permission::destroy($id);
    }
}