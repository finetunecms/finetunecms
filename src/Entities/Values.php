<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;

class Values extends Model
{

    protected $table = "ft_values";
    protected $primaryKey = "id";
    public $timestamps = true;

    protected $fillable = [
        'node_id', 'field_id', 'value'
    ];
    protected $guarded = array('id');


    public function field()
    {
        return $this->hasOne('\Finetune\Finetune\Entities\Fields', 'id', 'field_id');
    }

}
