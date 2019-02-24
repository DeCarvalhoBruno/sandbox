<?php namespace App\Models\Blog;

use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Model;

class BlogSource extends Model
{
    use DoesSqlStuff;

    public $timestamps = false;
    protected $primaryKey = 'blog_source_id';
    protected $fillable = ['blog_source_name'];


}