<?php namespace App\Support\Providers;

use App\Contracts\Models\SystemEventLog as SystemEventLogInterface;
use App\Models\System\SystemEvent;
use Carbon\Carbon;

/**
 * Class SystemEventLog
 * @method \App\Models\System\SystemEventLog createModel(array $attributes = [])
 */
class SystemEventLog extends Model implements SystemEventLogInterface
{

    protected $model = \App\Models\System\SystemEventLog::class;

    public function __construct()
    {
        parent::__construct();
    }

    public function getFromThisWeek()
    {
        $lastWeek = Carbon::now()->startOfWeek()->subWeek();
        return $this->select(['system_event_id', 'created_at', 'system_event_log_data'])
            ->where('created_at', '>', $lastWeek->toDateTimeString())
            ->orderBy('created_at', 'desc');
    }

    public function log(int $eventID, $data = null)
    {
        $createdAt = Carbon::now();
        $dbData = [
            'system_event_id' => $eventID,
            'created_at' => $createdAt->toDateTimeString()
        ];
        if (!is_null($data)) {
            $dbData['system_event_log_data'] = $this->formatDataForStorage($data, $eventID, $createdAt);
        }
        $this->createModel()->insert($dbData);
    }

    /**
     * @param array $data
     * @param int $eventID
     * @param \Carbon\Carbon $createdAt
     * @return \stdClass
     */
    public function formatDataForStorage(array $data, int $eventID, Carbon $createdAt)
    {
        $output = new \stdClass();
        switch ($eventID) {
            case SystemEvent::NEWSLETTER_SUBSCRIPTION:
                $output->icon = 'address-book';
                $output->title = [
                    'messages.newsletter_subscribed',
                    [
                        'user' => $data['full_name'],
                        'email' => $data['email']
                    ]
                ];
                $output->message = [];
                break;
            case SystemEvent::CONTACT_FORM_MESSAGE:
                $output->icon = 'envelope';
                $output->title = [
                    'messages.contact_form_message',
                    [
                        'email' => $data['contact_email']
                    ]
                ];
                $output->message = [
                    [
                        'messages.contact_form_subject',
                        ['subject' => $data['contact_subject']]
                    ],
                    [
                        'messages.contact_form_message_content',
                        ['message' => $data['message_body']]
                    ]
                ];
                break;
        }
        $output->time = $createdAt->format('H:i');
        $output->date = $createdAt->toDateString();
        return serialize($output);
    }

}