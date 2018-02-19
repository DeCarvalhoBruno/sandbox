<?php

namespace App\Models;

use App\Contracts\HasAnEntity;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use Illuminate\Database\Eloquent\Model;

class Person extends Model implements HasAnEntity
{
    use HasAnEntityTrait;

    public $table = 'people';
    public $primaryKey = 'person_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'user_id',
        'created_at'
    ];
    protected $hidden = [
        'person_id'
    ];
    protected $entityID = \App\Models\Entity::PEOPLE;


}
