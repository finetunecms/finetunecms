<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;


/**
 * Class fields
 * @package Entities
 */
class PasswordReminder extends Model
{
    /**
     * @var string
     */
    protected $table = "ft_password_reminders";
    /**
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * @var array
     */
    protected $fillable = [
        'email','token', 'created_at'
    ];
    /**
     * @var array
     */
    protected $guarded = array('id');

}