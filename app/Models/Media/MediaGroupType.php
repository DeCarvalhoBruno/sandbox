<?php namespace App\Models\Media;

use App\Traits\Models\HasASlugColumn;
use Illuminate\Database\Eloquent\Model;

class MediaGroupType extends Model
{
    use HasASlugColumn;

    protected $primaryKey = 'media_group_type_id';
    protected $fillable = [
        'media_group_type_id',
        'media_group_type_title',
        'media_group_id'
    ];
    /**
     * This model's name type column
     */
    public static $slugColumn = 'media_group_type_title';
}