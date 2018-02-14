<?php

namespace App\Models;

use App\Contracts\HasASystemEntity;
use App\Notifications\ResetPassword;
use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as LaravelUser;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\Models\HasASystemEntity as HasASystemEntityTrait;
use App\Traits\Models\HasANameColumn;

class User extends LaravelUser implements JWTSubject, HasASystemEntity
{
    use Notifiable, HasASystemEntityTrait, HasANameColumn, DoesSqlStuff;

    public $table = 'users';
    protected $primaryKey = 'user_id';
    public static $nameColumn = 'username';
    protected $fillable = [
        'username',
        'email',
        'password',
        'activated',
        'remember_token'
    ];
    protected $hidden = [
        'user_id',
        'password',
        'remember_token',
        'activated',
        'created_at',
        'updated_at',
        'person_id'
    ];
    protected $systemEntityID = \App\Models\SystemEntity::USERS;


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('person', function ($builder) {
            (new static)->scopePerson($builder);
        });
    }

    /**
     * Send the password reset notification.
     *
     * @param  string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopePerson(Builder $query)
    {
        return $this->join($query,Person::class);
    }

}
