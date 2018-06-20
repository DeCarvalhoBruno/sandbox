<?php namespace App\Events;

use App\Models\User;

class UserRegistered extends Event
{
    /**
     * @var string
     */
    private $token;
    /**
     * @var \App\Models\User
     */
    private $user;


    /**
     *
     * @param \App\Models\User $user
     * @param string $token
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * @return \App\Models\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }






}