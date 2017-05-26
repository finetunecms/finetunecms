<?php
namespace Finetune\Finetune\Repositories\Roles;

use Finetune\Finetune\Entities\Role;

class RolesRepository implements RolesInterface
{
    public function all()
    {
        $roles =  Role::with('perms')->where('usable','=',1)->get();
        $rolesArray = [];
        foreach($roles as $role){
            $role->parent_id = $this->find($role->parent_id);
            $rolesArray[] = $role;
        }
        return $rolesArray;
    }

    public function find($id)
    {
        return Role::whereNull('deleted_at')->find($id);
    }

    public function create($request)
    {
        $role = new Role();
        $role->parent_id = (isset($request['parent_id']) ? $request['parent_id']['id'] : 0);
        $role->name = $request['name'];
        $role->usable = 1;
        $role->deleteable = 1;
        $role->save();
        $role->perms()->sync([]);
        if(isset($request['perms'])){
            foreach($request['perms'] as $perm){
                $role->perms()->attach($perm['id']);
            }
        }
        return $this->all();
    }

    public function update($id, $request)
    {
        $role = $this->find($id);
        $role->parent_id = (isset($request['parent_id']) ? $request['parent_id']['id'] : 0);
        $role->name = $request['name'];
        $role->usable = 1;
        $role->deleteable = 1;
        $role->save();
        $role->perms()->sync([]);
        if(isset($request['perms'])){
            foreach($request['perms'] as $perm){
                $role->perms()->attach($perm['id']);
            }
        }
        return $this->all();
    }

    public function delete($id)
    {
        Role::destroy($id);
    }


}
