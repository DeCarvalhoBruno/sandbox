<?php namespace App\Support\Providers;

use App\Contracts\Models\Blog as BlogInterface;
use App\Models\Blog\BlogPost;

class Blog extends Model implements BlogInterface
{
    protected $model = \App\Models\Blog\BlogPost::class;


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
            ->where('blog_post_slug', '=', $slug);
    }

    /**
     * @param string $slug
     * @param array $data
     * @return int
     */
    public function updateOne($slug, $data)
    {
        return $this->createModel()->newQuery()->where('blog_post_slug', '=', $slug)
            ->update($this->filterFillables($data));
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