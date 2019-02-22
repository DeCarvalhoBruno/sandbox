<?php namespace App\Support\Providers;

use App\Contracts\Models\Blog as BlogInterface;
use App\Models\Blog\BlogPost;
use App\Contracts\Models\BlogCategory as CategoryInterface;
use App\Contracts\Models\BlogTag as TagInterface;

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
     *
     * @param \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory $c
     * @param \App\Contracts\Models\BlogTag|\App\Support\Providers\BlogTag $t
     */
    public function __construct(CategoryInterface $c, TagInterface $t)
    {
        parent::__construct();
        $this->category = $c;
        $this->tag = $t;
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

    public function buildForDisplay()
    {
$postList = [
    2,3,4,29,60,714,723,740,747,1001,1204,1206,1220,1520,1532,1751,1752,1757,1761,1787,1799,1801,2332,2929,3454,4039,103,456,1061,1270,1572,1832,1833,2364,3858,105,801,1849,2396,2402,2987,4124,203,524,824,1354,2708,3587,3593,4197,265,273,294,1661,1961,2505,3094,3370,3703,3965,4249,4261,326,682,1185,1737,1998,2246,2538,2582,2832,3171,3180,4007,4009,4012,4017
];
        return $this->select([
            'blog_post_title as title',
            'published_at as date',
            'blog_category_slug as cat',
            'full_name as author',
            'blog_post_is_sticky as featured',
            'entity_types.entity_type_id as type',
            'blog_post_slug as slug'
        ])->entityType()
            ->status()
            ->person()
            ->category()
//            ->where('language_id','=',1)
            ->whereIn('blog_posts.blog_post_id',$postList);

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
            ->category()
            ->where('blog_post_slug', '=', $slug);
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
        return $builder->select(['blog_post_id'])->first();
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