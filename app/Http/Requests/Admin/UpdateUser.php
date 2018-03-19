<?php

namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;
use App\Traits\ProcessesPermissions;

class UpdateUser extends FormRequest
{
    use ProcessesPermissions;

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

        if (isset($input['permissions'])) {
            $this->processPermissions($input['permissions']);
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
}
