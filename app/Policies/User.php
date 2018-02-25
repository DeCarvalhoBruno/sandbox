<?php

namespace App\Policies;

use App\Models\Entity;
use App\Models\PermissionMask;
use Illuminate\Auth\Access\HandlesAuthorization;

class User
{
    use HandlesAuthorization;

    /**
     * @var int The processed permissions for the user, in the form of a bit mask
     */
    private $defaultPermissions;

    public function __construct()
    {
        $this->defaultPermissions = PermissionMask::getDefaultPermission(
            auth()->user()->getAttribute('entity_type_id'),
            Entity::USERS
        );
    }

    public function view()
    {
        return ($this->defaultPermissions & \App\Models\User::PERMISSION_VIEW) !== 0;
    }

    public function update()
    {
        return ($this->defaultPermissions & \App\Models\User::PERMISSION_EDIT) !== 0;
    }
}
