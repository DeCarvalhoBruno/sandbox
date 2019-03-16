<?php namespace Naraki\Blog\Providers;

use App\Support\Providers\Model;
use Naraki\Blog\Contracts\Blog as BlogInterface;
use Naraki\Blog\Models\BlogPost;
use Naraki\Blog\Contracts\Category as CategoryInterface;
use Naraki\Blog\Contracts\Tag as TagInterface;
use Naraki\Blog\Contracts\Source as SourceInterface;

class Blog extends Model implements BlogInterface
{
    protected $model = \Naraki\Blog\Models\BlogPost::class;

    /**
     * @var \Naraki\Blog\Contracts\Category|\Naraki\Blog\Providers\Category
     */
    private $category;
    /**
     * @var \Naraki\Blog\Contracts\Tag|\Naraki\Blog\Providers\Tag
     */
    private $tag;
    /**
     * @var \Naraki\Blog\Contracts\Source|\Naraki\Blog\Providers\Source $source
     */
    private $source;

    /**
     *
     * @param \Naraki\Blog\Contracts\Category|\Naraki\Blog\Providers\Category $c
     * @param \Naraki\Blog\Contracts\Tag|\Naraki\Blog\Providers\Tag $t
     * @param \Naraki\Blog\Contracts\Source|\Naraki\Blog\Providers\Source $s
     */
    public function __construct(CategoryInterface $c, TagInterface $t, SourceInterface $s)
    {
        parent::__construct();
        $this->category = $c;
        $this->tag = $t;
        $this->source = $s;
    }

    /**
     * @return \Naraki\Blog\Contracts\Category|\Naraki\Blog\Providers\Category
     */
    public function category()
    {
        return $this->category;
    }

    /**
     * @return \Naraki\Blog\Contracts\Tag|\Naraki\Blog\Providers\Tag
     */
    public function tag()
    {
        return $this->tag;
    }

    /**
     * @return \Naraki\Blog\Contracts\Source|\Naraki\Blog\Providers\Source
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
        ])->scopes([
            'entityType',
            'status',
            'person',
            'category',
            'pageViews'
        ]);
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
     * @return \Naraki\Blog\Models\BlogPost
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
     * @return \Naraki\Blog\Models\BlogPost
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