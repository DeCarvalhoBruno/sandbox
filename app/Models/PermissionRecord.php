<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRecord extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'entity_id',
        'permission_target_id',
        'permission_store_id',
    ];

}
