<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\BlogCategory as BlogCategoryProvider;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Response;

class BlogCategory extends Controller
{
    public function index()
    {
        return \App\Support\Trees\BlogCategory::getTree();
    }

    /**
     * @param \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory $catRepo
     * @return \Illuminate\Http\Response|array
     */
    public function create(BlogCategoryProvider $catRepo)
    {
        $parent = $this->request->get('parent');
        $label = $this->request->get('label');
        if (is_null($label)) {
            return response(null, Response::HTTP_NO_CONTENT);
        }
        $newCat = $catRepo->createOne($label, $parent);
        if (is_null($newCat)) {
            return response(null, Response::HTTP_NO_CONTENT);
        }

        return ['id' => $newCat->getAttribute('blog_category_slug')];
    }

    /**
     * @param int $id
     * @param \App\Contracts\Models\BlogCategory|\App\Support\Providers\BlogCategory $catRepo
     * @return \Illuminate\Http\Response|array
     */
    public function update($id, BlogCategoryProvider $catRepo)
    {
        $cat = $catRepo->getCat($id);
        if (!is_null($cat)) {
            $label = $this->request->get('label');
            $cat->setAttribute('blog_category_name', $label);
            $cat->setAttribute('blog_category_slug', str_slug($label, '-', app()->getLocale()));
            $cat->save();
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function delete($id)
    {
        $model = \App\Models\Blog\BlogCategory::query()
            ->where('blog_category_slug', $id)->first();
        if (!is_null($model)) {
            $model->delete();
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }
}