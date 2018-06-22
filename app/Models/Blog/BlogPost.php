<?php namespace App\Models\Blog;

use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use Presentable;
//    public $timestamps = false;
    protected $primaryKey = 'blog_post_id';
    protected $fillable = [
        'user_id',
        'blog_post_status_id',
        'blog_post_title',
        'blog_post_content',
        'blog_post_excerpt'
    ];

}