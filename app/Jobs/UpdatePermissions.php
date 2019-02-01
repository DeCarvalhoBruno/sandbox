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
     * @throws \Throwable
     */
    public function handle()
    {
        try {
            \DB::transaction(function () {
                PermissionStore::query()->delete();
                Permission::assignToAll();
            });
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
        $this->delete();
    }
}
