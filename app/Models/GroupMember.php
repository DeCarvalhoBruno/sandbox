<?php namespace App\Models;

use App\Contracts\HasAnEntity;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model implements HasAnEntity
{
    use HasAnEntityTrait;

    const PERMISSION_ADD = 0b10;
    const PERMISSION_DELETE = 0b1000;

    protected $entityID = \App\Models\Entity::GROUP_MEMBERS;

    public $timestamps = false;

    protected $fillable=[
        'user_id',
        'group_id'
    ];

}
