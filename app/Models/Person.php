<?php

namespace App\Models;

use App\Contracts\HasASystemEntity;
use App\Traits\Models\HasASystemEntity as HasASystemEntityTrait;
use Illuminate\Database\Eloquent\Model;

class Person extends Model implements HasASystemEntity
{
    use HasASystemEntityTrait;

    public $table = 'people';
    public $primaryKey = 'person_id';
    public $timestamps = false;

    protected $fillable = [
        'person_first_name',
        'person_last_name',
        'user_id'
    ];
    protected $hidden = [
        'person_id'
    ];
    protected $systemEntityID = \App\Models\SystemEntity::PEOPLE;


}
