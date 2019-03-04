<?php

namespace App\Http\Controllers\Ajax\Admin\User;

use App\Contracts\Models\System as SystemProvider;
use App\Http\Controllers\Admin\Controller;
use App\Models\System\SystemSection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $systemRepo->userSettings()
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
        $settings['events'] = auth()->user()->getAttribute('system_events_subscribed');
        $events = $systemRepo->getEvents();

        return response([
            'existing' => $settings,
            'events' => $events
        ], Response::HTTP_OK);
    }
}
