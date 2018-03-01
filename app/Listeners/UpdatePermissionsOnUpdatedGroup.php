<?php

namespace App\Listeners;

use App\Models\PermissionStore;
use App\Support\Permissions\Permission;

class UpdatePermissionsOnUpdatedGroup
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        //Deleting all permissions and re-adding them including newly added/removed users
        PermissionStore::query()->delete();
        Permission::assignToAll();
    }
}
