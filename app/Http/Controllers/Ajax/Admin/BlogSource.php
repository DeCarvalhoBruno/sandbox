<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\BlogSource as BlogSourceProvider;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Response;

class BlogSource extends Controller
{
    /**
     * @param \App\Contracts\Models\BlogSource|\App\Support\Providers\BlogSource $sourceRepo
     * @return \Illuminate\Http\Response|array
     */
    public function create(BlogSourceProvider $sourceRepo)
    {
        $type = intval($this->request->get('type'));
        $content = $this->request->get('content');
        $slug = $this->request->get('blog_slug');

        if (is_null($content) || !\App\Models\Blog\BlogSource::isValidValue($type)) {
            return response([$type, $content, $slug], Response::HTTP_NO_CONTENT);
        }

        $sourceRepo->createOne($type, $content, $slug);

        return response(
            ['sources' => $sourceRepo->buildByBlogSlug($slug)->get()->toArray()],
            Response::HTTP_OK
        );
    }

    /**
     * @param string $id
     * @param string $slug
     * @param \App\Contracts\Models\BlogSource|\App\Support\Providers\BlogSource $sourceRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function destroy($id,$slug, BlogSourceProvider $sourceRepo)
    {
        $sourceRepo->deleteOne(intval($id));
        return response(
            ['sources' => $sourceRepo->buildByBlogSlug($slug)->get()->toArray()],
            Response::HTTP_OK
        );
    }
}