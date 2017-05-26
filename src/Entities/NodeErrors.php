<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;


/**
 * Class fields
 * @package Entities
 */
class NodeErrors extends Model
{
    /**
     * @var string
     */
    protected $table = "ft_node_errors";
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
        'node_id', 'error', 'link'
    ];
    /**
     * @var array
     */
    protected $guarded = array('id');

    public function node()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\Node', 'id', 'node_id');
    }

}