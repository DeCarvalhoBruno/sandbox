<?php namespace App\Models\Media;

use App\Traits\Models\HasANameColumn;
use Illuminate\Database\Eloquent\Model;

class MediaGroupType extends Model
{
    use HasANameColumn;

    protected $primaryKey = 'media_group_type_id';
    protected $fillable = [
        'media_group_type_id',
        'media_group_type_title',
        'media_group_id'
    ];
    /**
     * This model's name type column
     */
    public static $nameColumn = 'media_group_type_title';
}