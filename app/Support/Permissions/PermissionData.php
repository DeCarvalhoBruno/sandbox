<?php namespace App\Support\Permissions;

class PermissionData
{
    /**
     * @var int
     */
    private $target;
    /**
     * @var int
     */
    private $holder;
    /**
     * @var int
     */
    private $mask;

    /**
     *
     * @param $target
     * @param $holder
     * @param $mask
     */
    public function __construct($target, $holder, $mask)
    {
        $this->target = $target;
        $this->holder = $holder;
        $this->mask = $mask;
    }

    public function setTarget($value = null)
    {
        $this->target = $value;
    }

    /**
     * @return int
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return int
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @return int
     */
    public function getMask()
    {
        return $this->mask;
    }


}