<?php

namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;


/**
 * Class fields
 * @package Entities
 */
class Fields extends Model
{
    /**
     * @var string
     */
    protected $table = "ft_fields";
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
        'type_id', 'auto_complete', 'auto_focus', 'checked', 'disabled',
        'max', 'min', 'multiple', 'step', 'regex_pattern', 'placeholder',
        'readonly', 'required','label', 'name', 'type', 'values', 'class'
    ];
    /**
     * @var array
     */
    protected $guarded = array('id');

}