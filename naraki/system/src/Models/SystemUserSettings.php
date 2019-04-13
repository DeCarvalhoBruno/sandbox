<?php namespace Naraki\System\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUserSettings extends Model
{
    protected $primaryKey = 'system_user_setting_id';
    public $timestamps = false;

    protected $fillable = [
        'system_section_id',
        'system_events_subscribed',
        'system_email_subscribed',
        'user_id'
    ];

}
