<?php
namespace Finetune\Finetune\Entities;

use Zizaco\Entrust\Traits\EntrustUserTrait;
use \Illuminate\Notifications\Notifiable;
use \Illuminate\Foundation\Auth\User as Authenticatable;
use \Lab404\Impersonate\Models\Impersonate;

/**
 * Class User
 */
class User extends Authenticatable
{

    use EntrustUserTrait;

    use Impersonate;


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'id';
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ft_users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    /**
     * Set the guarded rows
     *
     * @var array
     */
    protected $guarded = array('id', 'password');

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function roles()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\Role', 'ft_assigned_roles', 'user_id', 'role_id');
    }

    public function sites()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\Site', 'ft_assigned_sites', 'user_id', 'site_id');
    }

}