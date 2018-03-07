<?php

namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;
use App\Traits\ProcessesPermissions;

class UpdateGroup extends FormRequest
{
    use ProcessesPermissions;

    public function rules()
    {
        return [
            'group_name' => 'required|regex:/^(\w){1,15}$/|unique:groups,group_name',
            'group_mask' => 'required|integer|min:100',
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();
        $this->processPermissions($input['permissions']);

        if (isset($input['new_group_'])) {
            $input['email'] = $input['new_email'];
        }

        unset($input['new_email'], $input['new_username'], $input['permissions']);
        $this->replace($input);
    }
}
