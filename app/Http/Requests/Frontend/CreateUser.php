<?php

namespace App\Http\Requests\Frontend;

use App\Support\Requests\FormRequest;
use Illuminate\Support\Facades\Validator;

class CreateUser extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'string|max:75',
            'last_name' => 'string|max:75',
            'username' => 'required|string|max:15|regex:/^(\w){1,}$/|unique:users',
            'email' => 'required|string|email|max:255|unique:people',
            'password' => 'required|string|min:8|confirmed',
            'stat_user_timezone' => 'nullable',
            'g-recaptcha' => 'captcha'
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();

        unset($input['password_confirmation'], $input['g-recaptcha']);
        $this->replace($input);
    }

    public function prepareForValidation()
    {
        Validator::extend('captcha', ['\App\Support\Vendor\GoogleRecaptcha', 'validate']);
        parent::prepareForValidation();
    }
}
