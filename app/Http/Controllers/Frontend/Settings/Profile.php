<?php namespace App\Http\Controllers\Frontend\Settings;

use App\Http\Controllers\Frontend\Controller;
use App\Http\Requests\Frontend\UpdateUser;
use App\Support\Frontend\Breadcrumbs;
use App\Support\Providers\User as UserProvider;

class Profile extends Controller
{
    /**
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(UserProvider $userProvider)
    {
        $user = auth()->user();
        return view('frontend.site.settings.panes.profile', [
            'user' => $user,
            'breadcrumbs' => Breadcrumbs::render([
                ['label' => trans('titles.routes.profile'), 'url' => route_i18n('profile')]
            ]),
            'avatars' => $userProvider->getAvatars($user->getKey())
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
            $notifications = $request->input('notifications');
            $rest = $request->except(['notifications']);
            if (!empty($rest)) {
                if(isset($rest['password'])){
                    $rest['password'] = bcrypt($rest['password']);
                }
                $userRepo->updateOneByUsername(
                    auth()->user()->getAttribute('username'),
                    $rest
                );
            }
        }
        return back()->with(
            'msg',
            ['type' => 'success', 'title' => trans('pages.profile.update_success')]
        );

    }

}