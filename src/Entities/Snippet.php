<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Snippet extends Model
{
    use SoftDeletes;

    protected $table = "ft_snippets";
    protected $primaryKey = "id";
    public $timestamps = true;

    protected $fillable = [
        'group_id','order','publish','author_id', 'tag',
        'title', 'body','image','site_id', 'link_type', 'link_internal', 'link_external'
    ];
    protected $guarded = array('id');

    public function snippet_groups()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\SnippetGroups', 'id', 'group_id');
    }
    public function media()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\Media', 'id', 'image');
    }
    public function node()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\Node', 'id', 'link_internal');
    }
}