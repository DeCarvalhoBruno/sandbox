<?php namespace App\Http\Controllers\Frontend\Settings;

use App\Http\Controllers\Frontend\Controller;
use App\Support\Frontend\Breadcrumbs;

class Account extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        $user = auth()->user();
        return view('frontend.site.settings.panes.account', [
            'user' => $user,
            'title' => trans('pages.profile.settings_title'),
            'breadcrumbs' => Breadcrumbs::render([
                ['label' => trans('titles.routes.account'), 'url' => route_i18n('account')]
            ])
        ]);
    }

}