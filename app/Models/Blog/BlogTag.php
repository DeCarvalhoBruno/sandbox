<?php namespace App\Models\Blog;

use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use DoesSqlStuff;

    protected $table = 'blog_post_tags';
    protected $primaryKey = 'blog_post_tag_id';
    protected $fillable = ['blog_post_tag_name', 'blog_post_tag_slug'];
    public $timestamps = false;

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLabelType(Builder $builder)
    {
        return $this->joinReverse($builder, BlogLabelType::class);
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $blogPostId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLabelRecord(Builder $builder, $blogPostId = null)
    {
        return BlogCategory::scopeLabelRecord($builder, $blogPostId);
    }
}