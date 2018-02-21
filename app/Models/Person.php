<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
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


}
