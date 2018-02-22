<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionStore extends Model
{

    public $timestamps = false;

    protected $fillable = ['permission_entity_id','permission_store_target_id','permission_store_holder_id','permission_store_mask'];

}
