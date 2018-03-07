<?php namespace App\Traits;

trait ProcessesPermissions
{
    private $permissions=null;

    /**
     * @param array $input
     */
    public function processPermissions($input)
    {
        if (isset($input['hasChanged']) && $input['hasChanged'] == "true") {
            $result = [];
            foreach ($input as $k => $v) {
                if ($v['hasChanged'] == 'true') {
                    $result[Entity::getConstant(strtoupper($k))] = $v['mask'];
                }
            }
            $this->permissions = $result;
        }
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

}