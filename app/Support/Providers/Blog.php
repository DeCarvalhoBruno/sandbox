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

    /**
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildList($attributes)
    {
        return $this->select($attributes)->user();
    }


    /**
     * @param string $slug
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildOneBySlug($slug, $attributes = null)
    {
        return $this->createModel()->newQuery()
            ->select($attributes)
            ->entityType()
            ->status()
            ->user()
            ->where('blog_post_slug', '=', $slug);
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
     * @param array $data
     * @param \App\Models\User $user
     * @return \App\Models\Blog\BlogPost
     */
    public function createOne($data, $user)
    {
        $userModel = $user->first();
        if (is_null($userModel)) {
            throw new \UnexpectedValueException('User account for blog post creation not found.');
        }
        $data['user_id'] = $userModel->getKey();
        $post = new BlogPost($data);
        $post->save();
        return $post;
    }

}