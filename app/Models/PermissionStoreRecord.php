<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionStoreRecord extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'permission_entity_id',
        'permission_store_id',
        'permission_target_id',
        'permission_holder_id',
        'permission_store_type_id'
    ];

}
