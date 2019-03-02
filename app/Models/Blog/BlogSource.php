<?php namespace App\Models\Blog;

use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Enumerable as EnumerableTrait;
use App\Contracts\Enumerable;

class BlogSource extends Model implements Enumerable
{
    use DoesSqlStuff, EnumerableTrait;

    public $timestamps = false;
    protected $primaryKey = 'blog_source_id';
    protected $fillable = ['blog_source_name'];

    const BLOG_SOURCE_RECORD_URL = 1;
    const BLOG_SOURCE_RECORD_IMG = 2;
    const BLOG_SOURCE_RECORD_FILE = 3;
}