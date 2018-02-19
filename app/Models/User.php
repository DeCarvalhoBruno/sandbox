<?php

namespace App\Models;

use App\Contracts\HasAnEntity;
use App\Notifications\ResetPassword;
use App\Traits\Models\DoesSqlStuff;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as LaravelUser;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use App\Traits\Models\HasANameColumn;

class User extends LaravelUser implements JWTSubject, HasAnEntity
{
    use Notifiable, HasAnEntityTrait, HasANameColumn, DoesSqlStuff;

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
        'password',
        'remember_token',
        'activated',
        'updated_at',
        'person_id'
    ];
    protected $sortable = [
        'full_name',
        'email'
    ];

    protected $entityID = \App\Models\Entity::USERS;
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('person', function ($builder) {
            (new static)->scopePerson($builder);
        });
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->diffForHumans();
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
     * @param mixed $query
     * @param \App\Filters\ThreadFilters $filters
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     *
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeActivated(Builder $query)
    {
        return $query->where('activated', '=', 1);

    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopePerson(Builder $query)
    {
        return $this->join($query, Person::class);
    }

    public function getColumnInfo($columns)
    {
        $sortable = array_flip($this->sortable);
        $result = [];
        foreach ($columns as $name => $label) {
            $result[$name] = ['name' => $name, 'label' => $label, 'sortable' => isset($sortable[$name])];
        }
        return $result;
    }

}
