<?php namespace App\Models\Blog;

use App\Support\NestedSet\NodeTrait;
use Illuminate\Database\Eloquent\Model;

class BlogPostCategory extends Model
{
    use NodeTrait;

    protected $table = 'blog_post_categories';
    protected $primaryKey = 'blog_post_category_id';
    protected $fillable = ['blog_post_category_name', 'blog_post_category_codename'];
    protected $hidden = ['parent_id', 'lft', 'rgt'];
    public $timestamps = false;


}