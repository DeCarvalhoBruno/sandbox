<?php

namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;

class UpdateGroup extends FormRequest
{
    public function rules()
    {
        return [
            'group_name' => 'required|regex:/^(\w){1,15}$/|unique:groups,group_name',
            'group_mask' => 'required|integer|min:100',
        ];
    }
}
