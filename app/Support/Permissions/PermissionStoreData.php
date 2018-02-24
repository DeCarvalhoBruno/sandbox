<?php namespace App\Support\Permissions;

class PermissionStoreData
{
    const DEFAULT = 1;
    const COMPUTED = 2;
    /**
     * @var \App\Support\Permissions\PermissionData[]|array
     */
    private $permissions;
    /**
     * @var int
     */
    private $type;

    /**
     *
     * @param int $type
     * @param PermissionData[] $permissions
     */
    public function __construct($type, array $permissions)
    {
        $this->type = $type;

        if ($this->type == self::COMPUTED) {
            array_map(function ($v) {
                $this->permissions[$v->getTarget()][] = $v;
            }, $permissions);
        } else {
            $this->permissions = $permissions;
        }

    }

    public function has($user)
    {
        return isset($this->permissions[$user]);
    }

    /**
     * @param $user
     * @return \App\Support\Permissions\PermissionData|\App\Support\Permissions\PermissionData[]
     */
    public function get($user=null)
    {
        return $this->permissions[$user] ?? $this->permissions;
    }

    /**
     * @return \App\Support\Permissions\PermissionData[]|array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

}