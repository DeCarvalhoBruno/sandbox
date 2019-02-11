<?php namespace App\Http\Controllers\Frontend;

use App\Events\PersonSentContactRequest;
use App\Http\Requests\Frontend\SendContactEmail;

class Frontend extends Controller
{
    public function contact()
    {
        return view('frontend.site.contact');

    }

    public function sendContactEmail(SendContactEmail $request)
    {
        event(new PersonSentContactRequest(
                $request->get('sender_email'),
                $request->get('email_subject'),
                $request->get('email_body')
            )
        );
        return redirect(route_i18n('home'))->with(
            'msg',
            ['type' => 'success', 'title' => trans('pages.contact.send_success')]);
    }

}