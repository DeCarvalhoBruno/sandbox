<?php namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogPostLabelType extends Model
{

    protected $primaryKey = 'blog_post_label_type_id';
    protected $fillable = ['blog_post_label_id'];
    public $timestamps = false;

}