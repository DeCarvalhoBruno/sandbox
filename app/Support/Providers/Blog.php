<?php namespace App\Support\Providers;

use App\Contracts\Models\Blog as BlogInterface;
use App\Models\Blog\BlogPost;
use App\Contracts\Models\BlogCategory as CategoryInterface;
use App\Contracts\Models\BlogTag as TagInterface;
use App\Contracts\Models\BlogSource as SourceInterface;

class Blog extends Model implements BlogInterface
{
    protected $model = \App\Models\Blog\BlogPost::class;

    /**
     * @var \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory
     */
    private $category;
    /**
     * @var \App\Contracts\Models\BlogTag|\App\Support\Providers\BlogTag
     */
    private $tag;
    /**
     * @var \App\Contracts\Models\BlogSource|\App\Support\Providers\BlogSource $source
     */
    private $source;


    /**
     *
     * @param \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory $c
     * @param \App\Contracts\Models\BlogTag|\App\Support\Providers\BlogTag $t
     * @param \App\Contracts\Models\BlogSource|\App\Support\Providers\BlogSource $s
     */
    public function __construct(CategoryInterface $c, TagInterface $t, SourceInterface $s)
    {
        parent::__construct();
        $this->category = $c;
        $this->tag = $t;
        $this->source = $s;
    }

    /**
     * @return \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory
     */
    public function category()
    {
        return $this->category;
    }

    /**
     * @return \App\Contracts\Models\BlogTag|\App\Support\Providers\BlogTag
     */
    public function tag()
    {
        return $this->tag;
    }

    /**
     * @return \App\Contracts\Models\BlogSource|\App\Support\Providers\BlogSource
     */
    public function source()
    {
        return $this->source;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildForDisplay()
    {
        return $this->select([
            'blog_post_title as title',
            'published_at as date',
            'blog_category_slug as cat',
            'full_name as author',
            'blog_post_is_sticky as featured',
            'entity_types.entity_type_id as type',
            'blog_post_slug as slug',
            'unq as page_views'
        ])->entityType()
            ->status()
            ->person()
            ->category()
            ->pageViews();
    }

    /**
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildList($attributes)
    {
        return $this->select($attributes)->person();
    }

    /**
     * @param string $slug
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildOneBySlug($slug, $attributes = ['*'])
    {
        return $this->createModel()->newQuery()
            ->select($attributes)
            ->entityType()
            ->status()
            ->person()
            ->where('blog_post_slug', '=', $slug);
    }

    public function buildOneBasic($attributes = ['*'])
    {
        return $this->createModel()->newQuery()
            ->select($attributes)
            ->entityType();
    }

    /**
     * @param array $data
     * @param \Illuminate\Database\Eloquent\Builder
     * @return \App\Models\Blog\BlogPost
     */
    public function createOne($data, $person)
    {
        $personModel = $person->first();
        if (is_null($personModel)) {
            throw new \UnexpectedValueException('Person for blog post creation not found.');
        }
        $data['person_id'] = $personModel->getKey();
        $post = new BlogPost($data);
        $post->save();
        return $post;
    }

    /**
     * @param string $slug
     * @param array $data
     * @return \App\Models\Blog\BlogPost
     */
    public function updateOne($slug, $data)
    {
        $builder = $this->createModel()->newQuery()->where('blog_post_slug', '=', $slug);
        (clone($builder))->update($this->filterFillables($data));
        return $builder->select(['blog_post_id','blog_post_slug','blog_status_id'])->first();
    }

    /**
     * @param int|array $slug
     * @return int
     */
    public function deleteBySlug($slug)
    {
        if (is_string($slug)) {
            return $this->createModel()->newQuery()->where('blog_post_slug', '=', $slug)->delete();
        } else {
            return $this->createModel()->newQuery()->whereIn('blog_post_slug', $slug)->delete();
        }
    }

}