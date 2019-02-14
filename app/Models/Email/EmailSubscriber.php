<?php namespace App\Models\Email;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmailSubscriber extends Model
{
    protected $primaryKey = 'email_subscriber_id';
    protected $fillable = [
        'email_subscriber_target_id',
        'email_subscriber_source_id',
        'email_event_id'
    ];
    public $timestamps = false;

    /**
     * @link https://laravel.com/docs/5.7/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeRecipientEntityType(Builder $query)
    {
        return $query->join('entity_types as recipients', 'recipients.entity_type_id', '=',
            'email_subscribers.email_subscriber_target_id');
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userID
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeUser(Builder $query, $userID = null)
    {
        return $query->join('users', function ($q) use ($userID) {
            $q->on('recipients.entity_type_target_id', '=', 'users.user_id');
            $q->where('recipients.entity_id', '=', Entity::USERS);
            if ( ! is_null($userID)) {
                $q->where('users.user_id', '=', $userID);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $entityID
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeEmailEvent(Builder $query, $entityID = null)
    {
        $query->join('email_events', 'email_events.email_event_id', '=', 'email_subscribers.email_event_id');
        if ( ! is_null($entityID)) {
            if (is_array($entityID)) {
                $query->whereIn('email_events.entity_id', $entityID);

            } else {
                $query->where('email_events.entity_id', '=', $entityID);

            }
        }

        return $query;
    }

}
