<?php namespace App\Models\Blog;

use App\Contracts\Enumerable as EnumerableContract;
use App\Contracts\HasAnEntity;
use App\Contracts\HasPermissions as HasPermissionsContract;
use App\Models\Entity;
use App\Models\Language;
use App\Models\Media\MediaEntity;
use App\Models\Person;
use App\Traits\Enumerable;
use App\Traits\Models\DoesSqlStuff;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use App\Traits\Models\HasPermissions;
use App\Traits\Presentable;
use CyrildeWit\EloquentViewable\Viewable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use CyrildeWit\EloquentViewable\Contracts\Viewable as ViewableContract;

class BlogPost extends Model implements HasPermissionsContract, EnumerableContract, HasAnEntity, ViewableContract
{
    use Presentable, Enumerable, HasPermissions, DoesSqlStuff, HasAnEntityTrait, Viewable;

    const PERMISSION_VIEW = 0b1;
    const PERMISSION_ADD = 0b10;
    const PERMISSION_EDIT = 0b100;
    const PERMISSION_DELETE = 0b1000;

//    public $timestamps = false;
    protected $primaryKey = 'blog_post_id';
    protected $fillable = [
        'person_id',
        'blog_status_id',
        'blog_post_title',
        'blog_post_slug',
        'blog_post_content',
        'blog_post_excerpt',
        'blog_post_is_sticky',
        'published_at'
    ];
    protected $hidden = [
        'person_id',
        'blog_status_id'
    ];
    protected $sortable = [
        'blog_post_title'
    ];
    public static $slugColumn = 'blog_post_slug';
    public $entityID = \App\Models\Entity::BLOG_POSTS;

    public static function boot()
    {
        parent::boot();

        static::creating(
            function ($model) {
                $model->blog_post_slug = slugify(
                    substr($model->blog_post_title, 0, 95)
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
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeStatus(Builder $builder)
    {
        return $builder->join(
            'blog_status',
            'blog_status.blog_status_id',
            '=',
            'blog_posts.blog_status_id'
        );
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $personSlug
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopePerson(Builder $builder, $personSlug = null): Builder
    {
        return $builder->join('people', function ($q) use ($personSlug) {
            $q->on('blog_posts.person_id', '=', 'people.person_id');
            if (!is_null($personSlug)) {
                $q->where('people.person_slug',
                    '=', $personSlug);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
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

    /**
     * @link https://laravel.com/docs/5.7/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLabelRecords(Builder $builder)
    {
        return $this->join($builder, BlogLabelRecord::class);
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLabelTypes(Builder $builder)
    {
        return $builder->join('blog_label_types',
            'blog_label_records.blog_label_type_id',
            '=',
            'blog_label_types.blog_label_type_id'
        );
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $categorySlug
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeCategories(Builder $builder, $categorySlug = null): Builder
    {
        return $builder->join('blog_categories', function ($q) use ($categorySlug) {
            $q->on('blog_categories.blog_label_type_id', '=', 'blog_label_types.blog_label_type_id');
            if (!is_null($categorySlug)) {
                $q->where('blog_category_slug', '=', $categorySlug);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $categorySlug
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeCategory(Builder $builder, $categorySlug = null)
    {
        return $builder->labelRecords()->labelTypes()->categories($categorySlug);

    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $tagSlug
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeTag(Builder $builder, $tagSlug = null)
    {
        return $builder->labelRecords()->labelTypes()->tags($tagSlug);

    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $tagSlug
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeTags(Builder $builder, $tagSlug = null): Builder
    {
        return $builder->join('blog_tags', function ($q) use ($tagSlug) {
            $q->on('blog_tags.blog_label_type_id', '=', 'blog_label_types.blog_label_type_id');
            if (!is_null($tagSlug)) {
                $q->where('blog_tag_slug', '=', $tagSlug);
            }
        });
    }


    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeImages($builder)
    {
        return MediaEntity::scopeImage($builder);
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopePageViews(Builder $builder)
    {
        return $builder->join('stat_page_views', 'entity_types.entity_type_id', '=',
            'stat_page_views.entity_type_id');
    }

    public function scopeLanguage($builder)
    {
        return $builder->where('language_id', '=', Language::getAppLanguageId());
    }


}