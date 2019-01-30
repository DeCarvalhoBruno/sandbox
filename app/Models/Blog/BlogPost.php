<?php namespace App\Models\Blog;

use App\Contracts\Enumerable as EnumerableContract;
use App\Contracts\HasAnEntity;
use App\Contracts\HasPermissions as HasPermissionsContract;
use App\Models\Entity;
use App\Models\User;
use App\Traits\Enumerable;
use App\Traits\Models\DoesSqlStuff;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use App\Traits\Models\HasPermissions;
use App\Traits\Presentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model implements HasPermissionsContract, EnumerableContract, HasAnEntity
{
    use Presentable, Enumerable, HasPermissions, DoesSqlStuff, HasAnEntityTrait;

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
        'blog_post_slug',
        'blog_post_content',
        'blog_post_excerpt',
        'blog_post_is_sticky',
        'published_at'
    ];
    protected $hidden = [
        'user_id',
        'blog_post_status_id'
    ];
    protected $sortable = [
      'blog_post_title'
    ];
    public static $nameColumn = 'blog_post_slug';

    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($model) {
                $model->blog_post_slug = str_slug(
                    substr($model->blog_post_title, 0, 95),
                    '-',
                    app()->getLocale()
                );

                $latestSlug =
                    static::select(['blog_post_slug'])
                        ->whereRaw(sprintf(
                                'blog_post_slug = "%s" or blog_post_slug LIKE "%s-%%"',
                                $model->blog_post_slug,
                                $model->blog_post_slug)
                        )
                        ->latest($model->getKeyName())
                        ->value('blog_post_slug');
                if ($latestSlug) {
                    $pieces = explode('-', $latestSlug);

                    $number = intval(end($pieces));

                    $model->blog_post_slug .= sprintf('-%s', ($number + 1));
                }
            }
        );
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeStatus(Builder $builder)
    {
        return $builder->join(
            'blog_post_status',
            'blog_post_status.blog_post_status_id',
            '=',
            'blog_posts.blog_post_status_id'
        );
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeUser(Builder $builder)
    {
        return $this->joinReverse($builder, User::class);
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int|null $blogPostId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeEntityType(Builder $builder, $blogPostId = null)
    {
        return $builder->join('entity_types', function ($q) use ($blogPostId) {
            $q->on('entity_types.entity_type_target_id', '=', 'blog_posts.blog_post_id')
                ->where('entity_types.entity_id', '=', Entity::BLOG_POSTS);
            if (!is_null($blogPostId)) {
                $q->where('entity_types.entity_type_target_id',
                    '=', $blogPostId);
            }
        });
    }

}