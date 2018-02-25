<?php namespace App\Models;

use App\Contracts\HasAnEntity;
use App\Traits\Models\DoesSqlStuff;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model implements HasAnEntity
{
    use HasAnEntityTrait, DoesSqlStuff;

    const PERMISSION_ADD = 0b10;
    const PERMISSION_DELETE = 0b1000;

    protected $entityID = \App\Models\Entity::GROUP_MEMBERS;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'group_id'
    ];

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeGroup(Builder $builder)
    {
        return $this->joinWithKey($builder, Group::class, 'group_id');
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeUser(Builder $builder)
    {
        return $this->joinWithKey($builder, User::class,'user_id');
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeEntityType(Builder $builder)
    {
        return $builder->join('entity_types', function ($q) {
            $q->on('entity_types.entity_type_target_id', '=', 'group_members.user_id')
                ->where('entity_types.entity_id','=', Entity::USERS);
        });
    }

}
