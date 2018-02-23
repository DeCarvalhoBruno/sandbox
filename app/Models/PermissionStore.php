<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionStore extends Model
{
    public $timestamps = false;

    protected $fillable = ['permission_store_name'];

}
