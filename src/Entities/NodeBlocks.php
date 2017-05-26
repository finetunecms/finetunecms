<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;

class NodeBlocks extends Model
{
    /**
     * @var string
     */
    protected $table = "ft_blocks";
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
        'node_id', 'name', 'content', 'image', 'title'
    ];
    /**
     * @var array
     */
    protected $guarded = array('id');

    // Relationships

    public function media()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\Media', 'id', 'image');
    }

    public function node()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\Node', 'id', 'node_id');
    }

    public function compile()
    {
        return \Blade::compileString($this->content);
    }
}