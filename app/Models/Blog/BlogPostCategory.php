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
        'blog_post_label_type_id',
        'blog_post_category_slug'
    ];
    protected $hidden = ['parent_id', 'lft', 'rgt'];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model){
            forward_static_call([static::class,'checkSlug'],$model);
        });

        static::updating(function($model){
            forward_static_call([static::class,'checkSlug'],$model);
        });
    }

    private static function checkSlug($model)
    {
        $col = 'blog_post_category_slug';
        $model->{$col} = str_slug(
            substr($model->blog_post_category_name, 0, 75),
            '-',
            app()->getLocale()
        );

        $latestSlug = static::select([$col])
            ->whereRaw(sprintf('%1$s = "%2$s" or %1$s LIKE "%2$s-%%"', $col, $model->{$col}))
            ->latest($model->getKeyName())
            ->pluck($col)->first();
        if (!is_null($latestSlug)) {
            $pieces = explode('-', $latestSlug);
            $number = intval(end($pieces));
            $model->{$col} .= sprintf('-%s', ($number + 1));
        }
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLabelType(Builder $builder)
    {
        return $this->joinReverse($builder, BlogPostLabelType::class);
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $blogPostId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopeLabelRecord(Builder $builder, $blogPostId = null)
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