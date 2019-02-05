<?php namespace Tests;

use App\Models\GroupMember;

trait CreatesDatabaseResources
{
    protected function create($class, $attributes = [], $times = null)
    {
        return factory('App\\Models\\' . $class, $times)->create($attributes);
    }

    protected function make($class, $attributes = [], $times = null)
    {
        return factory('App\\Models\\' . $class, $times)->make($attributes);
    }

    /**
     * @param int $times
     * @return \App\Models\User|\App\Models\User[]
     */
    protected function createUser($times = 1)
    {
        $u = [];
        for ($i = 0; $i < $times; $i++) {
            $u[$i] = $this->create('User');
            $this->create('Person', ['user_id' => $u[$i]->getAttribute('user_id')]);
        }
        return (count($u) === 1) ? $u[0] : $u;
    }

    /**
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     */
    protected function assignUserToGroup($user, $group)
    {
        if (is_array($user)) {
            foreach ($user as $item) {
                $members[] = ['user_id' => $item->user_id, 'group_id' => $group->group_id];
            }
        } else {
            $members = ['user_id' => $user->user_id, 'group_id' => $group->group_id];
        }
        GroupMember::insert($members);
    }

}