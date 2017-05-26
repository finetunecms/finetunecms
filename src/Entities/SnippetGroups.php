<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class SnippetGroups extends Model
{
    use SoftDeletes;

    protected $table = "ft_snippet_groups";
    protected $primaryKey = "id";
    public $timestamps = true;

    protected $fillable = [
        'site_id','tag','title','dscpn','publish'
    ];
    protected $guarded = array('id');

    public function snippet()
    {
        return $this->hasMany('\Finetune\Finetune\Entities\Snippet', 'group_id', 'id')->orderBy('order', 'asc');
    }
}