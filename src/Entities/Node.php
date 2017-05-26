<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Node
 * @package Entities
 */
class Node extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = "ft_node";
    /**
     * @var string
     */
    protected $primaryKey = "id";
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    protected $fillable = [
        'site_id', 'area', 'area_fk', 'parent', 'order', 'publish',
        'author_id', 'homepage', 'type_id', 'url_slug', 'title', 'dscpn',
        'keywords', 'meta_title', 'body', 'image', 'publish_on', 'output_body',
        'tag', 'consolidated', 'redirect','soft_publish','exclude'
    ];
    /**
     * @var array
     */
    protected $guarded = array('id');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\User', 'id', 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\Type', 'id', 'type_id');
    }

    public function media()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\Media', 'id', 'image');
    }

    public function parent_node()
    {
        return $this->belongsTo('\Finetune\Finetune\Entities\Node', 'parent');
    }

    public function area_node()
    {
        return $this->belongsTo('\Finetune\Finetune\Entities\Node', 'area_fk');
    }

    public function children(){
        return $this->hasMany('\Finetune\Finetune\Entities\Node', 'parent', 'id');
    }

    public function site()
    {
        return $this->belongsTo('\Finetune\Finetune\Entities\Site', 'site_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\Tagging', 'ft_node_tags', 'node_id', 'tag_id');
    }

    public function blocks()
    {
        return $this->hasMany('\Finetune\Finetune\Entities\NodeBlocks', 'node_id', 'id');
    }

    public function values(){
        return $this->hasMany('\Finetune\Finetune\Entities\Values', 'node_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\Role', 'ft_node_roles', 'node_id', 'role_id')->withPivot('can_edit');
    }

    public function compile()
    {

        return \Blade::compileString($this->body);
    }

}