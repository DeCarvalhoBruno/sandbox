<?php namespace Naraki\System\Controllers;

use Naraki\Core\Controllers\Admin\Controller;
use Illuminate\Http\Response;
use Naraki\System\Facades\System;
use Naraki\System\Models\SystemEvent;
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
        $settings = ['events' => null, 'email' => null];
        $dbSettings = System::userSettings()->getSettings($this->user->getKey())->select('system_events_subscribed as events',
            'system_email_subscribed as email')->first();
        if (!is_null($dbSettings)) {
            $settings = $dbSettings->toArray();
        }
        $eventsDb = System::getEvents();

        $events = [];
        foreach ($eventsDb as $event) {
            $events[] = ['id' => $event->getKey(), 'name' => SystemEvent::getConstantName($event->getKey(), true)];
        }

        return response([
            'existing' => $settings,
            'events' => $events
        ], Response::HTTP_OK);
    }
}
