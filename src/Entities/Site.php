<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;


class Site extends Model
{
    use SoftDeletes;

    protected $table = "ft_site";

    protected $primaryKey = "id";

    public $timestamps = true;

    protected $fillable = [
        'domain', 'title', 'dscpn', 'keywords', 'theme',
        'company', 'person', 'email', 'street', 'town', 'postcode',
        'tel', 'region', 'key', 'tag'
    ];
    protected $guarded = array('id');

    public function nodes()
    {
        return $this->hasMany('\Finetune\Finetune\Entities\Node', 'id', 'site_id');
    }

    public function users()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\User', 'ft_assigned_users', 'site_id', 'user_id');
    }
}