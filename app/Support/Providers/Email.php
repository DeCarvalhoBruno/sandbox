<?php namespace App\Support\Providers;

use App\Contracts\Models\Email as EmailInterface;
use App\Contracts\Models\EmailSubscriber as SubscriberInterface;
use App\Contracts\Models\EmailSchedule as ScheduleInterface;

class Email implements EmailInterface
{
    /**
     * @var \App\Contracts\Models\EmailSubscriber
     */
    protected $subscriber;
    /**
     * @var \App\Contracts\Models\EmailSchedule
     */
    protected $schedule;

    public function __construct(SubscriberInterface $sui, ScheduleInterface $sci){
        $this->subscriber = $sui;
        $this->schedule = $sci;
    }

    /**
     * @return \App\Contracts\Models\EmailSubscriber|\App\Support\Providers\EmailSubscriber
     */
    public function subscriber(){
        return $this->subscriber;
    }

    /**
     * @return \App\Contracts\Models\EmailSchedule|\App\Support\Providers\EmailSchedule
     */
    public function schedule(){
        return $this->schedule;
    }

}