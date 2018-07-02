<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Http\Controllers\Controller;

class BlogPostCategory extends Controller
{
    public function index()
    {
        return \App\Support\Trees\BlogPostCategory::getTree();
    }

    public function update($id)
    {
        $cat = \App\Models\Blog\BlogPostCategory::query()->where('blog_post_category_codename',$id);
        if(!is_null($cat)){
            $label = $this->request->get('label');

        }
    }

    public function delete($id)
    {

    }

}