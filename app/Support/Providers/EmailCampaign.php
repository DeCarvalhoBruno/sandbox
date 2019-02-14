<?php namespace App\Services\Email;

use App\Support\Providers\Model;
use App\Contracts\Models\EmailCampaign as CampaignInterface;
use App\Contracts\Models\EmailUserEvent as EmailUserEventInterface;

/**
 * @method \App\Models\Email\EmailCampaign createModel(array $attributes = [])
 */
class EmailCampaign extends Model implements CampaignInterface
{
    /**
     * @var \App\Contracts\Models\EmailUserEvent|\App\Support\Providers\EmailUserEvent
     */
    private $event;

    protected $model = \App\Models\Email\EmailCampaign::class;

    /**
     * EmailCampaign constructor.
     *
     * @param \App\Contracts\Models\EmailUserEvent|\App\Support\Providers\EmailUserEvent $euei
     * @param string $model
     */
    public function __construct(EmailUserEventInterface $euei, $model = null)
    {
        parent::__construct($model);
        $this->event = $euei;
    }

    /**
     * @return \App\Contracts\Models\EmailUserEvent|\App\Support\Providers\EmailUserEvent
     */
    public function event()
    {
        return $this->event;
    }

    /**
     * @param bool $export
     *
     * @return array
     */
    public function getFiltered($export = false)
    {
        $campaigns = $this->createModel()
            ->emailUserEvent()
            ->select([
                \DB::raw('count(user_id) as total'),
                \DB::raw('count(distinct(user_id)) as uniq'),
                'email_user_event_type_id as event',
                'email_campaign_name as name',
                'email_campaign_total_sent as sent',
                'email_campaigns.email_campaign_id as id'
            ])->groupBy('email_campaigns.email_campaign_id')
            ->groupBy('email_user_event_type_id')
            ->orderBy('email_campaigns.email_campaign_id', 'desc')
            ->get();
        $output = [];
        foreach ($campaigns as $campaign) {
            $output[$campaign->getAttribute('name')][$campaign->getAttribute('event')] =
                (object)$campaign->toArray();
            $output[$campaign->getAttribute('name')]['info'] =
                (object)$campaign->toArray();
        }

        return $output;
    }

}