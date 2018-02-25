<?php

namespace App\Policies;

use App\Models\Entity;
use App\Models\PermissionMask;
use Illuminate\Auth\Access\HandlesAuthorization;

class Group
{
    use HandlesAuthorization;

    /**
     * @var int The processed permissions for the user, in the form of a bit mask
     */
    private $defaultPermissions;

    public function __construct()
    {
//        $this->defaultPermissions = PermissionMask::getDefaultPermission(
//            auth()->user()->getAttribute('entity_type_id'),
//            Entity::GROUPS
//        );
    }

    public function view()
    {
//        return ($this->defaultPermissions & \App\Models\Group::PERMISSION_VIEW) !== 0;
        return true;
    }

    public function update()
    {
//        return ($this->defaultPermissions & \App\Models\Group::PERMISSION_EDIT) !== 0;
        return true;
    }
}
