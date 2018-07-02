<?php namespace App\Models\Blog;

use Illuminate\Database\Eloquent\Model;

class BlogPostLabelRecord extends Model
{
    protected $table = 'blog_post_label_records';
    protected $primaryKey = 'blog_post_label_record_id';
    protected $fillable = [
        'blog_post_id',
        'blog_post_label_target_id',
        'blog_post_label_type_id'
    ];
    public $timestamps = false;
}