<?php namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class MediaType extends Model {

	protected $table = 'media_types';
	public $timestamps = false;
	protected $primaryKey = 'media_type_id';
	protected $fillable = ['media_title','media_description','media_uuid','media_in_use'];

}
