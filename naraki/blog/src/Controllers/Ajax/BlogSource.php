<?php namespace Naraki\Blog\Controllers\Ajax;

use Naraki\Blog\Contracts\BlogSource as BlogSourceProvider;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Response;

class BlogSource extends Controller
{
    /**
     * @param \Naraki\Blog\Contracts\BlogSource|\Naraki\Blog\Providers\BlogSource $sourceRepo
     * @return \Illuminate\Http\Response|array
     */
    public function create(BlogSourceProvider $sourceRepo)
    {
        $type = intval($this->request->get('type'));
        $content = $this->request->get('content');
        $slug = $this->request->get('blog_slug');

        if (is_null($content) || !\Naraki\Blog\Models\BlogSource::isValidValue($type)) {
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
     * @param \Naraki\Blog\Contracts\BlogSource|\Naraki\Blog\Providers\BlogSource $sourceRepo
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