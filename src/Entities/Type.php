<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use SoftDeletes;

    protected $table = "ft_type";
    protected $primaryKey = "id";
    public $timestamps = true;

    protected $fillable = [
        'title', 'outputs', 'layout', 'order_by', 'nesting','children','date', 'today_future', 'today_past',
        'pagination', 'pagination_limit', 'rss', 'blocks', 'live','ordering', 'default_type'
    ];
    protected $guarded = array('id');

    public function node()
    {
        return $this->hasMany('\Finetune\Finetune\Entities\Node', 'type_id', 'id');
    }
    public function fields()
    {
        return $this->hasMany('\Finetune\Finetune\Entities\Fields', 'type_id', 'id');
    }
}