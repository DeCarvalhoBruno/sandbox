<?php namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogSourceRecord extends Model
{

    public $timestamps = false;
    protected $primaryKey = 'blog_source_record_id';
    protected $fillable = [
        'blog_post_id',
        'blog_source_id',
        'blog_source_record_type_id',
        'blog_source_content'
    ];

    const BLOG_STATUS_PUBLISHED = 3;

}