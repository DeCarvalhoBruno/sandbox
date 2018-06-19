<?php

namespace App\Jobs;

use App\Models\PermissionStore;
use App\Support\Permissions\Permission;

class UpdatePermissions extends Job
{

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \DB::beginTransaction();
            PermissionStore::query()->delete();
            Permission::assignToAll();
            \DB::commit();
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
        $this->delete();
    }
}
