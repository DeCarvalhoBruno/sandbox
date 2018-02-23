<?php namespace App\Models;

use App\Traits\EnumerableTrait;
use Illuminate\Database\Eloquent\Model;

class PermissionStoreType extends Model
{
    use EnumerableTrait;

    const DEFAULT = 1;
    const COMPUTED = 2;

    public $timestamps = false;

    protected $fillable = [
        'permission_store_target_id'
    ];

}
