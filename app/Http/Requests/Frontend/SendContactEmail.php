<?php

namespace App\Http\Requests\Frontend;

use App\Support\Requests\FormRequest;
use Illuminate\Support\Facades\Validator;

class SendContactEmail extends FormRequest
{
    public function rules()
    {
        return [
            'sender_email' => 'nullable',
            'email_subject' => 'nullable',
            'email_body' => 'nullable'
        ];
    }

    public function filters()
    {
        return [
            'email_subject' => 'strip_tags',
            'sender_email' => 'strip_tags',
            'email_body' => 'purify'
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();
        unset($input['g-recaptcha']);
        $this->replace($input);
    }

    public function prepareForValidation()
    {
        Validator::extend('captcha', call_user_func(['App\Support\Vendor\GoogleRecaptcha','validate']));
        parent::prepareForValidation();
    }
}
