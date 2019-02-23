<?php namespace App\Models\Email;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\HasAnEntity;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;


class Email extends Model implements HasAnEntity
{
    use HasAnEntityTrait;

    protected $primaryKey = 'email_id';
    protected $fillable = [
        'email_recipient_type_id',
        'email_content',
        'email_content_sources',
        'email_schedule_id'
    ];
    public $timestamps = false;
    public static $entityID =  \App\Models\Entity::EMAILS;

}