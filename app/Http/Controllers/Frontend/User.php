<?php namespace App\Http\Controllers\Frontend;

use App\Http\Requests\Frontend\UpdateUser;
use \App\Support\Providers\User as UserProvider;

class User extends Controller
{

    public function edit()
    {
        return view('frontend.site.user', [
            'user' => $this->user,
            'breadcrumbs' => [trans('titles.routes.profile') => route_i18n('profile')]
        ]);
    }

    /**
     * @param \App\Http\Requests\Frontend\UpdateUser $request
     * @param \Illuminate\Contracts\Auth\UserProvider|\App\Support\Providers\User $userRepo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUser $request, UserProvider $userRepo)
    {
        if (!empty($request->all())) {
            $userRepo->updateOneByUsername(auth()->user()->getAttribute('username'), $request->all());
        }
        return redirect(route_i18n('home'))->with(
            'msg',
            ['type' => 'success', 'title' => trans('pages.profile.update_success')]
        );

    }

}