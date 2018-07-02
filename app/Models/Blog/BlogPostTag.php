<?php namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogPostTag extends Model
{
    protected $table = 'blog_post_tags';
    protected $primaryKey = 'blog_post_tag_id';
    protected $fillable = ['blog_post_tag_name'];
    public $timestamps = false;
}