<?php namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;

class PersonMeta extends Model
{

    public $table = 'person_meta';
    public $primaryKey = 'person_meta_id';

    protected $fillable = [
        'person_id',
        'person_meta_url',
    ];

}