<?php namespace App\Services\Email;

use App\Support\Providers\Model;
use App\Contracts\Models\EmailUserEvent as EmailUserEventInterface;
use Illuminate\Support\Collection;

/**
 * @method \App\Models\Email\EmailUserEvent createModel(array $attributes = [])
 */
class EmailUserEvent extends Model implements EmailUserEventInterface
{

    protected $model = \App\Models\Email\EmailUserEvent::class;

    /**
     * @param int $id
     *
     * @return array
     */
    public function getFiltered($id)
    {
        $output = [];
        $events = $this->createModel()
            ->emailUserEventClick()
            ->where('email_campaign_id', $id)
            ->groupBy('entity_id')
            ->select([
                'email_user_click_entity as entity',
                'email_user_click_url as url',
                \DB::raw('count(email_user_click_id) as clicks')
            ])
            ->orderBy('clicks', 'desc')
            ->get();

        foreach ($events as $event) {
            $tmp = (object)$event->toArray();
            $tmp->entity = trans(sprintf(
                    'common.%s',
                    \App\Models\Entity::getModelPresentableName($tmp->entity))
            );
            $tmp->url = sprintf('https://%s/%s/%s', env('APP_DOMAIN'), $tmp->slug,
                $tmp->url);
            $output[$event->getAttribute('d')][] = $tmp;
        }
        $name = $this->createModel()->emailCampaign()
            ->select(['email_campaign_name'])->value('email_campaign_name');
        return new Collection(['output' => $output, 'name' => $name]);
    }
}