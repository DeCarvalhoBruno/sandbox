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

    public function buildForDisplay()
    {
        $postList = [
            2,3,4,6,11,20,45,52,381,410,714,723,740,1001,1024,1034,1203,1220,1230,1514,1520,1757,1761,1799,1805,1807,1808,2093,2304,2315,2332,2333,2902,2919,2929,3226,3454,3477,3779,3801,4036,4086,4647,5244,5279,5544,5552,5554,6051,6081,6257,6266,6274,6285,6301,6857,6873,7458,7475,7824,8036,8076,8079,8686,8687,8689,8705,8968,8985,9519,9520,9546,9553,9571,9781,9787,10150,10371,10374,10696,10927,10950,10984,10991,11583,11905,12109,12729,13051,456,1833,2973,3512,3846,4103,4432,4701,4705,5582,5605,6092,6101,6320,6333,6642,6914,7847,8455,10436,10451,11012,11342,12164,12165,13073,13090,135,154,801,1297,2379,2702,2981,3021,4124,4767,6374,6948,7556,8167,8173,8763,8783,8793,9340,9909,11040,11059,11350,12176,12214,12805,12823,203,205,209,210,853,1352,1354,1626,2161,2449,2708,3593,3600,4197,4222,4789,5384,5888,5892,6433,6434,7035,7629,7635,7637,8222,8232,8234,8507,9071,9650,10192,10216,10508,10512,11103,11689,11695,11699,11711,11720,12236,12525,12857,12866,12876,12879,13111,13126,262,265,287,294,299,566,602,921,1130,1137,1383,1430,1661,1928,1961,1965,2475,2492,2505,3094,3660,3679,3700,3703,3965,4240,4261,4285,4847,4862,4866,4873,4879,5123,5161,5447,5709,5913,5932,5964,6171,6444,6447,6470,7056,7087,7647,7661,7666,7673,7684,7692,8253,8255,8273,8276,8281,8295,8544,8863,8888,8901,9162,9403,9410,9424,9457,10002,10583,11140,11163,11172,11436,11730,11741,11745,12293,12314,12326,12331,12339,12340,12932,12937,12942,12947,310,326,349,662,1185,1727,1737,1985,2551,2562,2582,2588,2593,2823,2828,3148,3171,3177,3398,3722,4009,4012,4017,4292,4594,4889,4901,4914,5465,5737,5752,5980,5993,6565,6797,7413,7417,7724,7740,8009,8926,9179,10066,10325,10625,10635,10871,10893,11193,11208,11217,11243,11826,12357,12386,12391,12664,12677,12955,13003,13227
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
            ->whereIn('blog_posts.blog_post_id', $postList);

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