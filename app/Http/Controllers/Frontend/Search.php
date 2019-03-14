<?php namespace App\Http\Controllers\Frontend;

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
}