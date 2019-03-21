<?php

namespace Naraki\System\Requests;

use App\Support\Requests\FormRequest;
use App\Traits\ProcessesPermissions;

class UpdateGroup extends FormRequest
{
    use ProcessesPermissions;

    public function rules()
    {
        return [
            'new_group_name' => 'regex:/^(\w){1,15}$/|unique:groups,group_name',
            'group_mask' => 'required|integer|min:100',
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();
        $this->processPermissions($input['permissions']);

        if (isset($input['new_group_name'])) {
            $input['group_name'] = $input['new_group_name'];
        }

        unset($input['new_group_name'], $input['permissions']);
        $this->replace($input);
    }
}
