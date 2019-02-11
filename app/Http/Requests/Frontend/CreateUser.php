<?php

namespace App\Http\Requests\Frontend;

use App\Support\Requests\FormRequest;
use App\Support\Vendor\GoogleRecaptcha;
use Illuminate\Support\Facades\Validator;

class CreateUser extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'string|max:75',
            'last_name' => 'string|max:75',
            'username' => 'required|string|max:15|regex:/^(\w){1,}$/|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
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
        Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
            $result= GoogleRecaptcha::check($value,'localhost',env('RECAPTCHA_SECRET_KEY'));
            if($result===true){
                return true;
            }
            $validator->errors()->add('recaptcha','');
            return false;
        });
        parent::prepareForValidation();
    }
}
