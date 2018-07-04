<?php namespace App\Support\Providers;

use App\Contracts\Models\Blog as BlogInterface;
use App\Models\Blog\BlogPost;
use App\Contracts\Models\BlogCategory as CategoryInterface;

class Blog extends Model implements BlogInterface
{
    protected $model = \App\Models\Blog\BlogPost::class;
    /**
     * @var \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory
     */
    private $category;

    public function __construct(CategoryInterface $c)
    {
        parent::__construct();
        $this->category = $c;
    }

    /**
     * @return \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory
     */
    public function category()
    {
        return $this->category;
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