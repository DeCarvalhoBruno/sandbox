<?php namespace App\Models;

use App\Contracts\HasAnEntity;
use App\Traits\Models\HasANameColumn;
use App\Traits\Models\HasAnEntity as HasAnEntityTrait;
use Illuminate\Database\Eloquent\Model;

class Group extends Model implements HasAnEntity
{
    use HasAnEntityTrait, HasANameColumn;

    const PERMISSION_VIEW = 0b1;
    const PERMISSION_ADD = 0b10;
    const PERMISSION_EDIT = 0b100;
    const PERMISSION_DELETE = 0b1000;

    public $primaryKey = 'group_id';
    protected $entityID = \App\Models\Entity::GROUPS;
    protected $nameColumn = 'group_name';
    protected $fillable = [
        'group_name',
        'group_mask',
        'group_id'
    ];

    public $timestamps = false;

}
