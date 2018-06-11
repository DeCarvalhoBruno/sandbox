<?php namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class MediaDigital extends Model
{
    protected $table = 'media_digital';
    protected $primaryKey = 'media_digital_id';
    protected $fillable = ['media_type_id','media_filename','media_extension','media_thumbnail'];

}