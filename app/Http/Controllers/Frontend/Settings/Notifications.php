<?php namespace App\Http\Controllers\Frontend\Settings;

use App\Contracts\Models\Email as EmailProvider;
use App\Http\Controllers\Frontend\Controller;
use App\Models\Email\EmailList;
use App\Support\Frontend\Breadcrumbs;

class Notifications extends Controller
{
    /**
     * @param \App\Contracts\Models\Email|\App\Support\Providers\Email $emailRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(EmailProvider $emailRepo)
    {
        $user = auth()->user();
        return view('frontend.site.settings.panes.notifications', [
            'user' => $user,
            'breadcrumbs' => Breadcrumbs::render([
                [
                    'label' => trans('titles.routes.notifications'),
                    'url' => route_i18n('notifications')
                ]
            ]),
            'mailing_lists' => EmailList::getList(),
            'subscribed' => array_flip(
                $emailRepo->subscriber()
                    ->buildAllUser(
                $user->getAttribute('person_id'),
                ['email_lists.email_list_id']
                    )->pluck('email_list_id')->toArray())
        ]);
    }

}