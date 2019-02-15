<?php namespace App\Support\Providers;

use App\Contracts\Models\EmailSubscriber as SubscriberInterface;
use App\Models\Email\EmailSubscriber as SubscriberModel;
use App\Models\Entity;
use App\Models\EntityType;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method \App\Models\Email\EmailSubscriber createModel(array $attributes = [])
 */
class EmailSubscriber extends Model implements SubscriberInterface
{
    protected $model = \App\Models\Email\EmailSubscriber::class;

    /**
     * @param int $personID
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildAllUser($personID, $columns = ['*']): Builder
    {
        return $this->createModel()->newQuery()
            ->recipientEntityType()
            ->emailList()
            ->person()
            ->user($personID)
            ->select($columns);
    }

    public function addUserToLists($personID, $list)
    {
        if (is_null($list)) {
            $savedList = [];
        } else {
            $savedList = array_keys($list);
        }

        $currentUserLists = $this->buildAllUser(
            $personID,
            ['email_lists.email_list_id'])
            ->pluck('email_list_id')->toArray();

        $listsToRemove = array_diff($currentUserLists, $savedList);
        $listsToAdd = array_diff($savedList, $currentUserLists);
        if (empty($listsToAdd) && empty($listsToRemove)) {
            return;
        }
        $targetID = EntityType::getEntityTypeID(Entity::PEOPLE, intval($personID));

        if (!is_null($targetID)) {
            if (!empty($listsToRemove)) {
                SubscriberModel::query()
                    ->where('email_subscriber_target_id', '=', $targetID)
                    ->whereIn('email_list_id', $listsToRemove)->delete();
            }

            if (!empty($listsToAdd)) {
                $subscriberDb = [];
                foreach ($listsToAdd as $listId) {
                    $subscriberDb[] = [
                        'email_subscriber_target_id' => $targetID,
                        'email_list_id' => $listId
                    ];
                }
                SubscriberModel::insert($subscriberDb);
            }
        }
    }

}