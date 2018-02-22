<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionEntity extends Model
{

    public $primaryKey = 'permission_entity_id';
    public $timestamps = false;

    protected $fillable = ['entity_id'];



}
