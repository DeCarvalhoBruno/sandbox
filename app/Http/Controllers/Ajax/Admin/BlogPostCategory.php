<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\BlogCategory as BlogCategoryProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class BlogPostCategory extends Controller
{
    public function index()
    {
        return \App\Support\Trees\BlogPostCategory::getTree();
    }

    /**
     * @param \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory $catRepo
     * @return \Illuminate\Http\Response|array
     */
    public function create(BlogCategoryProvider $catRepo)
    {
        $id = $this->request->get('id');
        $label = $this->request->get('label');
        if (is_null($label)) {
            return response('', Response::HTTP_NO_CONTENT);
        }
        $newCat = $catRepo->createOne($label, $id);
        if (is_null($newCat)) {
            return response(null, Response::HTTP_NO_CONTENT);
        }

        return ['id' => $newCat->getAttribute('blog_post_category_codename')];

    }

    public function update($id)
    {
        $cat = $this->getCat($id);
        if (!is_null($cat)) {
            $label = $this->request->get('label');
            $cat->setAttribute('blog_post_category_name', $label);
            $cat->setAttribute('blog_post_category_slug', str_slug($label,'-',app()->getLocale()));
            $cat->save();
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function delete($id)
    {
        if (is_hex_uuid_string($id)) {
            $model = \App\Models\Blog\BlogPostCategory::query()
                ->where('blog_post_category_codename', $id)->first();
            if (!is_null($model)) {
                $model->delete();
            }
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $id
     * @return \App\Models\Blog\BlogPostCategory|null
     */
    private function getCat($id)
    {
        if (is_hex_uuid_string($id)) {
            return \App\Models\Blog\BlogPostCategory::query()
                ->where('blog_post_category_codename', $id)->first();
        }
        return null;
    }

}