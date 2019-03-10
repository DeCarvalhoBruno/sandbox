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
        $input = $request->get('q');
        if (!is_null($input && !empty($input))) {
            $blog = ElasticSearch::search()
                ->index('naraki.blog_posts.en')
                ->type('main')
                ->from(0)
                ->size(5)
                ->matchPhrasePrefix('title', strip_tags($input))->get()->source();
            $author = ElasticSearch::search()
                ->index('naraki.blog_authors.en')
                ->from(0)
                ->size(5)
                ->type('main')
                ->matchPhrasePrefix('name', strip_tags($input))->get()->source();
            $tag = ElasticSearch::search()
                ->index('naraki.blog_tags.en')
                ->type('main')
                ->from(0)
                ->size(5)
                ->matchPhrasePrefix('name', strip_tags($input))->get()->source();
            return response(['status' => 'ok', 'blog' => $blog, 'author' => $author, 'tag' => $tag], Response::HTTP_OK);
        }
        return response(['stats' => null, Response::HTTP_OK]);

    }

}