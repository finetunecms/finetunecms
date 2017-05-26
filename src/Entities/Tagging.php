<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Tagging extends Model
{
    use SoftDeletes;

    protected $table = "ft_tags";
    protected $primaryKey = "id";
    public $timestamps = true;

    protected $fillable = [
        'site_id', 'title', 'tag'
    ];
    protected $guarded = array('id');

    public function nodes()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\Node', 'ft_node_tags', 'tag_id', 'node_id');
    }
}
