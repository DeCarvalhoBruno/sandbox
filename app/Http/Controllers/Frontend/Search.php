<?php namespace App\Http\Controllers\Frontend;

use App\Support\Database\ElasticSearch\Facades\ElasticSearch;
use Illuminate\Http\Response;

class Search extends Controller
{
    /**
     * @param string $q
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function get($q=null)
    {
        $q = strip_tags($q);
        return view('frontend.site.search', compact('q'));
    }

    /**
     * @param string $source
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function post($source=null)
    {
        $input = app('request')->get('q');
        if (!is_null($input && !empty($input))) {
            list($blog, $author, $tag) = [
                is_null($source)?$this->searchBlog($input):$this->searchBlogPaginate($input),
                $this->searchAuthor($input),
                $this->searchTag($input)
            ];
            return response([
                'status' => 'ok',
                'headers' => [
                    'articles' => trans('pages.blog.search_headers.articles'),
                    'authors' => trans('pages.blog.search_headers.authors'),
                    'tags' => trans('pages.blog.search_headers.tags'),
                    'no_result' => trans('pages.blog.search_headers.no_result'),
                    'more_results' => trans('pages.blog.search_headers.more_results',
                        ['search' => mb_strtoupper($input)])
                ],
                'articles' => $blog,
                'authors' => $author,
                'tags' => $tag
            ], Response::HTTP_OK);
        }
        return response(['stats' => null, Response::HTTP_OK]);

    }

    /**
     * @param string $input
     * @param int $size
     * @return array
     */
    private function searchBlog($input, $size = 4)
    {
        return ElasticSearch::search()
            ->index('naraki.blog_posts.en')
            ->type('main')
            ->from(0)
            ->size($size)
            ->matchPhrasePrefix('title', strip_tags($input))->get()->source();
    }

    /**
     * @param string $input
     * @param int $size
     * @return \App\Support\Database\ElasticSearch\Results\Paginator
     */
    private function searchBlogPaginate($input, $size=8)
    {
        return ElasticSearch::search()
            ->index('naraki.blog_posts.en')
            ->type('main')
            ->matchPhrasePrefix('title', strip_tags($input))->paginate($size,false);

    }

    /**
     * @param string $input
     * @param int $size
     * @return array
     */
    private function searchAuthor($input, $size = 4)
    {
        return ElasticSearch::search()
            ->index('naraki.blog_authors.en')
            ->from(0)
            ->size($size)
            ->type('main')
            ->matchPhrasePrefix('name', strip_tags($input))->get()->source();
    }

    /**
     * @param string $input
     * @param int $size
     * @return array
     */
    private function searchTag($input, $size = 4)
    {
        return ElasticSearch::search()
            ->index('naraki.blog_tags.en')
            ->type('main')
            ->from(0)
            ->size($size)
            ->matchPhrasePrefix('name', strip_tags($input))->get()->source();
    }
}