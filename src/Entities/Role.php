<?php
namespace Finetune\Finetune\Entities;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    public function nodes()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\Node', 'ft_node_roles', 'role_id', 'node_id');
    }
}