<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;


/**
 * Class fields
 * @package Entities
 */
class Redirect extends Model
{
    /**
     * @var string
     */
    protected $table = "ft_redirects";
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
        'old','new'
    ];
    /**
     * @var array
     */
    protected $guarded = array('id');

}