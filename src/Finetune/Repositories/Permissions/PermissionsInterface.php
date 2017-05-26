<?php

namespace Finetune\Finetune\Repositories\Permissions;

interface PermissionsInterface
{

    public function all();

    public function find($id);

    public function create($input);

    public function update($id,$input);

    public function delete($id);
}