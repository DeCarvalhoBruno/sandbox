<?php namespace App\Models\Email;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $primaryKey = 'email_id';
    protected $fillable = [
        'email_recipient_type_id',
        'email_content',
        'email_content_sources',
        'email_schedule_id'
    ];
    public $timestamps = false;

}