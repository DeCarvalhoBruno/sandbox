<?php

namespace App\Http\Requests\Frontend;

use App\Support\Requests\FormRequest;

class UpdateUser extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'max:75',
            'last_name' => 'max:75',
            'username' => 'nullable|regex:/^(\w){1,}$/|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'confirmed',
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();
        $keptInput = [];
        foreach ($input as $k => $v) {
            if (isset($v) && !empty($v)) {
                $keptInput[$k] = $v;
            }
        }
        $this->replace($keptInput);
    }

}
