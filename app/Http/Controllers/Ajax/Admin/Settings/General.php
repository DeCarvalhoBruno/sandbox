<?php

namespace App\Http\Controllers\Ajax\Admin\Settings;

use App\Models\System\SystemEvent;
use App\Models\System\SystemSection;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Response;
use App\Support\Providers\User as UserProvider;
use App\Contracts\Models\System as SystemProvider;

class General extends Controller
{
    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request $request
     * @param \App\Contracts\Models\System| \App\Support\Providers\System $systemRepo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SystemProvider $systemRepo)
    {
        $systemRepo->settings()
            ->save($this->user->getKey(), SystemSection::BACKEND, $request->all());
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \App\Contracts\Models\System| \App\Support\Providers\System $systemRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function edit(SystemProvider $systemRepo)
    {
        $settings = [];
        $subscribed = auth()->user()->getAttribute('system_events_subscribed');
        $events = $systemRepo->getEvents();

        if (!is_null($subscribed) && !empty($subscribed)) {
            $settings['events'] = explode(',', $subscribed);
        } else {
            $settings['events'] = [];
        }

        return response([
            'existing' => $settings,
            'events' => $events
        ], Response::HTTP_OK);
    }
}
