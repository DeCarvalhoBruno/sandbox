<?php namespace Naraki\Blog\Controllers\Ajax;

use Naraki\Blog\Contracts\Category as BlogCategoryProvider;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Response;

class BlogCategory extends Controller
{
    public function index()
    {
        return \Naraki\Blog\Support\Trees\Category::getTree();
    }

    /**
     * @param \Naraki\Blog\Contracts\Category|\Naraki\Blog\Providers\Category $catRepo
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
     * @param \Naraki\Blog\Contracts\Category|\Naraki\Blog\Providers\Category $catRepo
     * @return \Illuminate\Http\Response|array
     */
    public function update($id, BlogCategoryProvider $catRepo)
    {
        $cat = $catRepo->getCat($id);
        if (!is_null($cat)) {
            $label = $this->request->get('label');
            $cat->setAttribute('blog_category_name', $label);
            $cat->setAttribute('blog_category_slug', slugify($label, '-', app()->getLocale()));
            $cat->save();
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function delete($id)
    {
        $model = \Naraki\Blog\Models\BlogCategory::query()
            ->where('blog_category_slug', $id)->first();
        if (!is_null($model)) {
            $model->delete();
        }
        return response(null, Response::HTTP_NO_CONTENT);
    }
}