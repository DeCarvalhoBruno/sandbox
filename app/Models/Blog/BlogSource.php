<?php namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogSource extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'blog_source_id';
    protected $fillable = ['blog_source_name'];

}