<?php namespace App\Models\Email;

use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class EmailEvent extends Model
{
    use Enumerable;

    protected $primaryKey = 'email_event_id';
    protected $fillable = ['email_event_name', 'entity_id'];
    public $timestamps = false;

//    const BLOG_POSTS_DIGEST = 0x12c;    //300
    const NEWSLETTER = 0x7d0;              //2000

}
