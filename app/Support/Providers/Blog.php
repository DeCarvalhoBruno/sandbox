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
    2,3,4,747,6329,2246,2929,5798,5554,4363,5514,1998,1801,6091,3679,4418,29,294,4873,4822,4524,1761,1751,4007,6910,3171,456,2505,1061,2708,4249,4711,6470,1752,1001,203,103,2402,4261,3180,4012,3965,4009,5737,1206,2538,1220,60,723,2332,524,4124,6613,1757,265,3454,682,3370,1572,1737,1270,5582,4017,4564,5846,2582,273,2987,1661,1833,154,824,3703,2396,3587,3779,4492,5123,326,5752,4039,4879,4866,1799,6909,1185,5980,3593,4889,4651,1787,4197,5251,2832,4646,1354,1532,4594,6655,3858,714,4638,4719,6642,5384,801,1849,5169,1832,5608,6565,2364,1520,6325,3094,6274,4705,740,4647
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