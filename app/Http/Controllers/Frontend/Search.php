<?php namespace App\Http\Controllers\Frontend;

class Search extends Controller
{
    /**
     * @param string $q
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function get($q = null)
    {
        $q = strip_tags($q);
        $search_url = env('ELASTIC_SEARCH_URL');
        return view('frontend.site.search', compact('q', 'search_url'));
    }
}