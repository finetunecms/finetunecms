<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;

/**
 * Class fields
 * @package Entities
 */
class FailedLogins extends Model
{
    /**
     * @var string
     */
    protected $table = "ft_failedlogins";
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
        'ip', 'failed_logins', 'locked_out', 'expire_time', 'last_attempt'
    ];
    /**
     * @var array
     */
    protected $guarded = array('id');

}