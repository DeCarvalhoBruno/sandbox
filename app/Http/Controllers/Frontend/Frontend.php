<?php namespace App\Http\Controllers\Frontend;

use App\Events\PersonSentContactRequest;
use App\Events\UserSubscribedToNewsletter;
use App\Http\Requests\Frontend\SendContactEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function newsletterSubscribe(Request $request)
    {
        $input = $request->only('full_name', 'email');
        if (isset($input['full_name']) && isset($input['email'])) {
            event(new UserSubscribedToNewsletter($input));
        }
        return response([
            'title' => trans('titles.subscribed_msg_title'),
            'text' => trans('titles.subscribed_msg_text')
        ], Response::HTTP_OK);


    }

}