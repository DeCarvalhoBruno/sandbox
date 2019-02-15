<?php namespace App\Support\Providers;

use App\Contracts\Models\Email as EmailInterface;
use App\Contracts\Models\EmailSubscriber as SubscriberInterface;
use App\Contracts\Models\EmailSchedule as ScheduleInterface;
use App\Contracts\Models\EmailList as ListInterface;
use App\Models\Email\EmailRecipientType;
use App\Models\Email\EmailList;
use Illuminate\Support\Str;

class Email extends Model implements EmailInterface
{
    protected $model = \App\Models\Email\Email::class;
    /**
     * @var \App\Contracts\Models\EmailSubscriber|\App\Support\Providers\EmailSubscriber
     */
    protected $subscriber;
    /**
     * @var \App\Contracts\Models\EmailSchedule|\App\Support\Providers\EmailSchedule
     */
    protected $schedule;
    /**
     * @var \App\Contracts\Models\EmailList|\App\Support\Providers\EmailList
     */
    protected $list;

    public function __construct(
        SubscriberInterface $sui,
        ScheduleInterface $sci,
        ListInterface $eli,
        $model = null
    ) {
        parent::__construct($model);
        $this->subscriber = $sui;
        $this->schedule = $sci;
        $this->list = $eli;
    }

    /**
     * @return \App\Contracts\Models\EmailSubscriber|\App\Support\Providers\EmailSubscriber
     */
    public function subscriber(): SubscriberInterface
    {
        return $this->subscriber;
    }

    /**
     * @return \App\Contracts\Models\EmailSchedule|\App\Support\Providers\EmailSchedule
     */
    public function schedule(): ScheduleInterface
    {
        return $this->schedule;
    }

    /**
     * @return \App\Contracts\Models\EmailList|\App\Support\Providers\EmailList
     */
    public function list(): ListInterface
    {
        return $this->list;
    }

    /**
     * Gets content to be displayed in emails. Calls the appropriate function based
     * on the type of email being sent.
     *
     * @param int $targetID
     * @param int $emailEventID
     *
     * @return mixed
     * @throws \Exception
     */
    public function yieldEmailContent($targetID, $emailEventID = null)
    {
        $collection = call_user_func(
            [$this, Str::camel(EmailList::getConstantName($emailEventID))],
            $targetID
        );
        if (!$collection->has('recipient_type')) {
            $collection->put('recipient_type', EmailRecipientType::ALL);
        }

        return $collection;
    }

}