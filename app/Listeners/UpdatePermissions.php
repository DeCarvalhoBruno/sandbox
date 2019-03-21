<?php

namespace App\Listeners;

class UpdatePermissions extends Listener
{
    /**
     * Deleting all permissions and re-adding them including newly added/removed users
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->dispatch(new \Naraki\Permission\Jobs\Update);
    }
}
