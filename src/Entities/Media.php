<?php
namespace Finetune\Finetune\Entities;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;


class Media extends Model
{

    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = "ft_media";
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
        'site_id', 'filename', 'extension', 'path', 'external', 'thumb',
        'original', 'title', 'mime', 'type', 'height', 'width', 'parent',
        'version', 'order'
    ];

    // Relationships

    public function folders()
    {
        return $this->belongsToMany('\Finetune\Finetune\Entities\Folders', 'ft_media_folders', 'media_id', 'folders_id');
    }

    public function nodes()
    {
        return $this->hasMany('\Finetune\Finetune\Entities\Node', 'image', 'id');
    }

}