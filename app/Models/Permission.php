<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    public $primaryKey = 'permission_id';
    public $timestamps = false;
    protected $fillable = ['entity_type_id','entity_id'];

}
