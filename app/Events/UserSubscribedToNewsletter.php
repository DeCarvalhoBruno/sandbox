<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSubscribedToNewsletter implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $input;

    /**
     * Create a new event instance.
     *
     * @param $input
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return $this->input;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('general');
//        return new Channel('general');

    }

    public function broadcastAs()
    {
        return 'emailing.subscription';
    }

    public function broadcastWith()
    {
        return ['input'=>$this->input];
    }




}
