<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;


class Folders extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = "ft_folders";
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
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = [
        'side_it', 'title', 'tag',
    ];

    // Relationships

    public function media()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\Media', 'ft_media_folders', 'folders_id', 'media_id')->orderBy('order');
    }
}