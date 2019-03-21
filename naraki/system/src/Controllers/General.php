<?php namespace Naraki\System\Controllers;

use App\Http\Controllers\Admin\Controller;
use Illuminate\Http\Response;
use Naraki\System\Facades\System;
use Naraki\System\Models\SystemSection;

class General extends Controller
{
    /**
     * Update the user's password.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        System::userSettings()
            ->save($this->user->getKey(), SystemSection::BACKEND, app('request')->all());
        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function edit()
    {
        $settings = [];
        $settings['events'] = $this->user->getAttribute('system_events_subscribed');
        $events = System::getEvents();

        return response([
            'existing' => $settings,
            'events' => $events
        ], Response::HTTP_OK);
    }
}
