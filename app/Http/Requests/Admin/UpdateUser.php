<?php

namespace App\Http\Requests\Admin;

use App\Models\Entity;
use App\Support\Requests\FormRequest;

class UpdateUser extends FormRequest
{
    private $permissions = null;

    public function rules()
    {
        return [
            'new_email' => 'email|unique:users,email',
            'new_username' => 'regex:/^(\w){1,15}$/|unique:users,username',
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();

        $pe = $input['permissions'];
        if (isset($pe['hasChanged']) && $pe['hasChanged'] == "true") {
            $result = [];
            foreach ($pe as $k => $v) {
                if ($v['hasChanged'] == 'true') {
                    $result[Entity::getConstant(strtoupper($k))] = $v['mask'];
                }
            }
            $this->permissions = $result;
        }

        if (isset($input['new_email'])) {
            $input['email'] = $input['new_email'];
        }
        if (isset($input['new_username'])) {
            $input['username'] = $input['new_username'];
        }
        unset($input['new_email'], $input['new_username'], $input['permissions']);
        $this->replace($input);
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
