<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivation extends Model
{
    public $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'activation_token'
    ];

}