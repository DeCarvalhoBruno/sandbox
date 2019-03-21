<?php namespace Naraki\Permission\Jobs;

use App\Jobs\Job;
use App\Support\Permissions\Permission;
use Naraki\Permission\Models\PermissionStore;

class Update extends Job
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
