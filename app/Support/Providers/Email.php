<?php namespace App\Support\Providers;

use App\Contracts\Models\Email as EmailInterface;
use App\Contracts\Models\EmailSubscriber as SubscriberInterface;
use App\Contracts\Models\EmailSchedule as ScheduleInterface;
use App\Models\Email\EmailRecipientType;

class Email extends Model implements EmailInterface
{
    protected $model = \App\Models\Email\Email::class;
    /**
     * @var \App\Contracts\Models\EmailSubscriber
     */
    protected $subscriber;
    /**
     * @var \App\Contracts\Models\EmailSchedule
     */
    protected $schedule;

    public function __construct(SubscriberInterface $sui, ScheduleInterface $sci, $model = null)
    {
        parent::__construct($model);
        $this->subscriber = $sui;
        $this->schedule = $sci;
    }

    /**
     * @return \App\Contracts\Models\EmailSubscriber|\App\Support\Providers\EmailSubscriber
     */
    public function subscriber()
    {
        return $this->subscriber;
    }

    /**
     * @return \App\Contracts\Models\EmailSchedule|\App\Support\Providers\EmailSchedule
     */
    public function schedule()
    {
        return $this->schedule;
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
            [$this, Str::camel(EmailEventModel::getConstantName($emailEventID))],
            $targetID
        );
        if (!$collection->has('recipient_type')) {
            $collection->put('recipient_type', EmailRecipientType::ALL);
        }

        return $collection;
    }

}