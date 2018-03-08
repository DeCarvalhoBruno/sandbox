<?php

namespace App\Policies;

use App\Models\Entity;
use App\Models\PermissionMask;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User as UserModel;

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
        return ($this->defaultPermissions & UserModel::PERMISSION_VIEW) !== 0;
    }

    public function update()
    {
        return ($this->defaultPermissions & UserModel::PERMISSION_EDIT) !== 0;
    }

    public function delete()
    {
        return ($this->defaultPermissions & UserModel::PERMISSION_DELETE) !== 0;
    }
}
