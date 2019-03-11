<?php namespace App\Http\Controllers\Frontend;

use App\Support\Database\ElasticSearch\Facades\ElasticSearch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Search extends Controller
{
    public function get($q)
    {

    }

    public function post(Request $request)
    {
        $size=4;
        $input = $request->get('q');
        if (!is_null($input && !empty($input))) {
            $blog = ElasticSearch::search()
                ->index('naraki.blog_posts.en')
                ->type('main')
                ->from(0)
                ->size($size)
                ->matchPhrasePrefix('title', strip_tags($input))->get()->source();
            $author = ElasticSearch::search()
                ->index('naraki.blog_authors.en')
                ->from(0)
                ->size($size)
                ->type('main')
                ->matchPhrasePrefix('name', strip_tags($input))->get()->source();
            $tag = ElasticSearch::search()
                ->index('naraki.blog_tags.en')
                ->type('main')
                ->from(0)
                ->size($size)
                ->matchPhrasePrefix('name', strip_tags($input))->get()->source();
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

}