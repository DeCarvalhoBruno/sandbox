<?php namespace App\Models\Blog;

use App\Support\NestedSet\NodeTrait;
use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogPostCategory extends Model
{
    use NodeTrait, DoesSqlStuff;

    protected $table = 'blog_post_categories';
    protected $primaryKey = 'blog_post_category_id';
    protected $fillable = [
        'blog_post_category_name',
        'blog_post_category_codename',
        'blog_post_label_type_id'
    ];
    protected $hidden = ['parent_id', 'lft', 'rgt'];
    public $timestamps = false;

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLabelType(Builder $builder)
    {
        return $this->joinReverse($builder, BlogPostLabelType::class);
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $blogPostId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLabelRecord(Builder $builder, $blogPostId = null)
    {
        return $builder->join('blog_post_label_records', function ($q) use ($blogPostId) {
            $q->on(
                'blog_post_label_types.blog_post_label_type_id',
                '=',
                'blog_post_label_records.blog_post_label_type_id'
            );
            if (!is_null($blogPostId)) {
                $q->where('blog_post_id', '=', $blogPostId);
            }
        });
    }
}