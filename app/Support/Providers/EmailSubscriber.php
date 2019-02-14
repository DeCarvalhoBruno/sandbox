<?php namespace App\Services\Email;

use App\Models\Email\EmailSubscriber as EmailSubscriberModel;
use App\Models\Entity;
use App\Models\EntityType;
use App\Support\Providers\Model;
use App\Contracts\Models\EmailSubscriber as SubscriberInterface;

/**
 * @method \App\Models\Email\EmailSubscriber createModel(array $attributes = [])
 */
class EmailSubscriber extends Model implements SubscriberInterface
{
    protected $model = \App\Models\Email\EmailSubscriber::class;

    /**
     * @param int $userID
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAllUser($userID, $columns = ['*'])
    {
        return $this->createModel()->newQuery()
            ->recipientEntityType()
            ->where('email_subscriber_source_id', 0)
            ->where('email_event_id', '<', 1000)
            ->user($userID)
            ->select($columns);
    }

    /**
     * @param int $userID
     * @param array $subscriptions
     */
    public function updateUser($userID, $subscriptions)
    {
        $currentSubscriptions = $this->buildAllUser($userID, ['email_subscribers.*'])->get();
        $currentEvents = [];
        $targetID = null;
        if (!$currentSubscriptions->isEmpty()) {
            $currentEvents = $currentSubscriptions->pluck(
                'email_event_id', 'email_subscriber_id')->toArray();
            $targetID = $currentSubscriptions->first()->getAttribute('email_subscriber_target_id');
        }

        $toRemove = array_diff($currentEvents, $subscriptions);
        $toAdd = array_diff($subscriptions, $currentEvents);

        if (!empty($toAdd)) {
            if (is_null($targetID)) {
                $targetID = EntityType::getEntityTypeID(Entity::USERS, $userID);
            }
            if (!is_null($targetID)) {
                $newElems = [];
                foreach ($toAdd as $item) {
                    $newElems[] = [
                        'email_subscriber_target_id' => $targetID,
                        'email_subscriber_source_id' => 0,
                        'email_event_id' => $item
                    ];
                }
                EmailSubscriberModel::insert($newElems);
            } else {
                throw new \UnexpectedValueException('No user found for email subscription update');
            }
        }

        if (!empty($toRemove)) {
            EmailSubscriberModel::destroy(array_keys($toRemove));
        }
    }

    /**
     * @param array $columns
     *
     * @return array
     */
    public function getAllForMailingList($columns = ['*'])
    {
        $model = $this->createModel();
        $subscribers = $model->newQuery()->recipientEntityType()
            ->where('email_subscriber_source_id', 0)
            ->user()
            ->where('websites.id', '>', 9)
            ->emailEvent()
            ->where('email_events.email_event_id', '<', 1000)
            ->select([
                'users.user_id as user_id',
                'first_name',
                'last_name',
                'users.email',
                'users.website_id',
                'slug',
                'websites.name as website_name',
                'email_events.system_entity_id',
                'email_event_name',
                'email_events.email_event_id'
            ])
            ->get();
        $subscriberLists = [];
        foreach ($subscribers as $subscriber) {
            $address = sprintf('%s.%s@mailgun-domain.com',
                $subscriber->getAttribute('slug'), $subscriber->getAttribute('email_event_name'));
            $subscriberLists[$address][strtolower(trim(str_replace(' ', '',
                $subscriber->getAttribute('email'))))] = (object)[
                'address' => $subscriber->getAttribute('email'),
                'name' => $subscriber->getAttribute('first_name') . ' ' . $subscriber->getAttribute('last_name'),
                'vars' => [
                    'id' => $subscriber->getAttribute('user_id'),
                    'site' => $subscriber->getAttribute('website_name'),
                    'slug' => $subscriber->getAttribute('slug')
                ]
            ];
        }

        return $subscriberLists;
    }

    /**
     * @param int $userID
     * @param array $events
     */
    public function addToDefaultLists($userID, $events)
    {
        $targetID = EntityType::getEntityTypeID(Entity::USERS, $userID);
        if (!is_null($targetID)) {
            if (!is_array($targetID)) {
                $targetID = [$targetID];
            }
            $defaults = [];
            foreach ($events as $event) {
                foreach ($targetID as $target) {
                    $defaults[] = [
                        'email_subscriber_target_id' => $target,
                        'email_subscriber_source_id' => 0,
                        'email_event_id' => $event->getAttribute('email_event_id')
                    ];
                }
            }
            EmailSubscriberModel::insert($defaults);
        }
    }


}