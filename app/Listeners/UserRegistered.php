<?php namespace App\Listeners;

class UserRegistered extends Listener
{
    /**
     * Deleting all permissions and re-adding them including newly added/removed users
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $this->dispatch(new \App\Jobs\UpdatePermissions);
    }

}