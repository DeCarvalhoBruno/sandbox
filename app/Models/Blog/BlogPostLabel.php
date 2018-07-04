<?php namespace App\Models\Blog;

use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class BlogPostLabel extends Model
{
    use Enumerable;

    protected $primaryKey = 'blog_post_label_id';
    protected $fillable = ['blog_post_label_name'];
    public $timestamps = false;

    const BLOG_POST_TAG = 1;
    const BLOG_POST_CATEGORY = 2;
}