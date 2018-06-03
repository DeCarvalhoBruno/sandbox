<?php namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class MediaType extends Model {

	protected $table = 'media_types';
	public static $thumbnailColumn = 'media_type_thumbnail';
	public $timestamps = false;
	protected $primaryKey = 'media_type_id';
	protected $fillable = ['media_type_title','media_type_description','media_type_thumbnail'];

}
