<?php namespace App\Models\Blog;

use App\Contracts\HasPermissions as HasPermissionsContract;
use App\Traits\Enumerable;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Enumerable as EnumerableContract;
use App\Traits\Models\HasPermissions;

class BlogPost extends Model implements HasPermissionsContract, EnumerableContract
{
    use Presentable, Enumerable, HasPermissions;

    const PERMISSION_VIEW = 0b1;
    const PERMISSION_ADD = 0b10;
    const PERMISSION_EDIT = 0b100;
    const PERMISSION_DELETE = 0b1000;

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