<?php

namespace App\Http\Requests\Frontend;

use App\Support\Requests\FormRequest;

class CreateUser extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:75',
            'last_name' => 'required|string|max:75',
            'username' => 'required|string|max:15|regex:/^(\w){1,}$/|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();
        unset($input['password_confirmation']);
        $this->replace($input);
    }
}
