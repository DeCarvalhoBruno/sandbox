<?php namespace App\Models\Blog;

use App\Contracts\Enumerable;
use App\Traits\Enumerable as EnumerableTrait;
use Illuminate\Database\Eloquent\Model;

class BlogSourceRecordType extends Model implements Enumerable
{
    use EnumerableTrait;

    public $timestamps = false;
    protected $primaryKey = 'blog_source_record_type_id';
    protected $fillable = ['blog_source_record_type_name'];

    const BLOG_SOURCE_RECORD_URL = 1;
    const BLOG_SOURCE_RECORD_IMG = 2;
    const BLOG_SOURCE_RECORD_FILE = 3;

}