<?php namespace App\Models;

use App\Contracts\HasAnEntity;
use App\Traits\Enumerable;
use App\Traits\Models\DoesSqlStuff;
use App\Traits\Models\HasASlug;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use Naraki\Permission\Traits\HasPermissions as HasPermissionsTrait;
use Naraki\Permission\Contracts\HasPermissions;
use App\Traits\Models\Presentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Contracts\Enumerable as EnumerableContract;
use Illuminate\Database\Query\JoinClause;

class Group extends Model implements HasAnEntity, HasPermissions, EnumerableContract
{
    use HasAnEntityTrait, HasASlug, Enumerable, Presentable, DoesSqlStuff, HasPermissionsTrait;

    const PERMISSION_VIEW = 0b1;
    const PERMISSION_ADD = 0b10;
    const PERMISSION_EDIT = 0b100;
    const PERMISSION_DELETE = 0b1000;

    public $primaryKey = 'group_id';
    public static $entityID = \App\Models\Entity::GROUPS;
    public static $slugColumn = 'group_name';
    protected $fillable = [
        'group_name',
        'group_mask',
    ];
    protected $sortable = [
        'group_name',
    ];

    public $timestamps = false;

    /**
     * @link https://laravel.com/docs/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeGroupMember(Builder $builder)
    {
        return $this->join($builder, GroupMember::class);
    }

    /**
     * @link https://laravel.com/docs/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLeftGroupMember(Builder $builder)
    {
        return $this->leftJoin($builder, GroupMember::class);
    }

    /**
     * @link https://laravel.com/docs/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeUser(Builder $builder)
    {
        return $builder->join('users', 'users.user_id', '=', 'group_members.user_id')
            ->join('people', 'users.user_id', '=', 'people.user_id');
    }

    /**
     * @link https://laravel.com/docs/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeEntityType(Builder $builder)
    {
        return $builder->join('entity_types', function (JoinClause $q) {
            $q->on('entity_types.entity_type_target_id', '=', 'groups.group_id')
                ->where('entity_types.entity_id', '=', Entity::GROUPS);
        });
    }

    /**
     * @link https://laravel.com/docs/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopePermissionRecord(Builder $builder)
    {
        return User::scopePermissionRecord($builder, Entity::GROUPS);
    }

    /**
     * @link https://laravel.com/docs/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopePermissionStore(Builder $builder)
    {
        return User::scopePermissionStore($builder);
    }

    /**
     * @link https://laravel.com/docs/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public static function scopePermissionMask(Builder $builder, $userId)
    {
        return User::scopePermissionMask($builder, $userId);
    }

}
