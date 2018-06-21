<?php namespace App\Models\Media;

class BlogPostStatus
{
    public $timestamps = false;
    protected $table = 'blog_post_status';
    protected $primaryKey = 'blog_post_status_id';
    protected $fillable = ['blog_post_status_name'];

    const BLOG_POST_STATUS_DRAFT=1;
    const BLOG_POST_STATUS_REVIEW=2;
    const BLOG_POST_STATUS_PUBLISHED=3;

}