<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionMask extends Model
{
    public $timestamps = false;

    protected $fillable = ['permission_store_id','permission_holder_id','permission_mask'];

}
