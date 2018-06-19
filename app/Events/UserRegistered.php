<?php namespace App\Events;

use App\Models\User;

class UserRegistered extends Event
{
    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return \App\Models\User
     */
    public function getUser(): \App\Models\User
    {
        return $this->user;
    }




}