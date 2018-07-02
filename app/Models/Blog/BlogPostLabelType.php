<?php namespace App\Models\Blog;

use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class BlogPostLabelType extends Model
{
    use Enumerable;

    protected $table = 'blog_post_label_types';
    protected $primaryKey = 'blog_post_label_type_id';
    protected $fillable = ['blog_post_label_type_name'];
    public $timestamps = false;

    const BLOG_POST_TAG = 1;
    const BLOG_POST_CATEGORY = 2;
}