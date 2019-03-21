<?php namespace Naraki\System\Models;

use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class SystemEvent extends Model
{
    use Enumerable;

    const NEWSLETTER_SUBSCRIPTION = 1;
    const CONTACT_FORM_MESSAGE = 2;

    public $timestamps = false;
    protected $primaryKey = 'system_event_id';

    protected $fillable = [
        'system_event_name'
    ];

}
