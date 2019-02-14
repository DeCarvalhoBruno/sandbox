<?php namespace App\Models\Email;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmailSchedule extends Model
{
    protected $primaryKey = 'email_schedule_id';
    protected $fillable = [
        'email_schedule_source_id',
        'email_schedule_name',
        'email_event_id',
        'email_schedule_send_at',
        'email_schedule_periodicity',
    ];
    public $timestamps = false;

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
        return $query->join('email_events', function ($q) use ($entityID) {
            $q->on('email_events.email_event_id', '=', 'email_schedules.email_event_id');
            if ( ! is_null($entityID)) {
                $q->where('entity_id', '=', $entityID);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/5.7/eloquent#query-scopes
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $targetID
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeSourceEntityType(Builder $query, $targetID = null)
    {
        return $query->join('entity_types', function ($q) use ($targetID) {
            $q->on('email_schedules.email_schedule_source_id', '=', 'entity_types.entity_type_id');
            if ( ! is_null($targetID)) {
                $q->where('entity_type_target_id', '=', $targetID);
            }
        });
    }
}
