<?php

namespace App\Models;

use App\Contracts\HasAnEntity;
use App\Notifications\ResetPassword;
use App\Traits\Enumerable;
use App\Traits\Models\DoesSqlStuff;
use App\Traits\Models\HasPermissions as HasPermissionsTrait;
use App\Contracts\HasPermissions;
use App\Traits\Presentable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as LaravelUser;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use App\Traits\Models\HasANameColumn;

class User extends LaravelUser implements JWTSubject, HasAnEntity, HasPermissions
{
    use Notifiable, HasAnEntityTrait, HasANameColumn, DoesSqlStuff, Enumerable, Presentable, HasPermissionsTrait;

    const PERMISSION_VIEW = 0b1;
    const PERMISSION_ADD = 0b10;
    const PERMISSION_EDIT = 0b100;
    const PERMISSION_DELETE = 0b1000;

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
     * @return int
     */
    public function getEntityType()
    {
        return $this->getAttribute('entity_type_id');

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
        return $this->getAttribute('username');
    }

    public function getIdentifier()
    {
        return 'users.username';
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

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeEntityType(Builder $builder, $userId = null)
    {
        return $builder->join('entity_types', function ($q) use ($userId) {
            $q->on('entity_types.entity_type_target_id', '=', 'users.user_id')
                ->where('entity_types.entity_id', '=', Entity::USERS);
            if (!is_null($userId)) {
                $q->where('entity_types.entity_type_target_id',
                    '=', $userId);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $entityId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopePermissionRecord(Builder $builder, $entityId = Entity::USERS)
    {
        return $builder->join('permission_records', function ($q) use ($entityId) {
            $q->on('permission_records.permission_target_id', '=', 'entity_types.entity_type_id')
                ->where('permission_records.entity_id', '=', $entityId);
        });
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopePermissionStore(Builder $builder)
    {
        return $builder->join('permission_stores',
            'permission_stores.permission_store_id',
            '=',
            'permission_records.permission_store_id'
        );
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopePermissionMask(Builder $builder, $userId)
    {
        return $builder->join('permission_masks', function ($q) use ($userId) {
            $q->on('permission_masks.permission_store_id', '=', 'permission_records.permission_store_id')
                ->where('permission_masks.permission_holder_id', '=', $userId)
                ->where('permission_mask', '>', 0);
        });
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $groupName
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopeGroupMember(Builder $builder, $groupName = null)
    {
        return $builder->join('group_members',
            'group_members.user_id',
            '=',
            'users.user_id'
        )->join('groups', function ($q) use ($groupName) {
            $q->on('group_members.group_id', '=', 'groups.group_id');
            if (!is_null($groupName)) {
                $q->where('group_name', '=', $groupName);
            }
        });
    }

}
