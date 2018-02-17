<?php

namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;

class UpdateUser extends FormRequest
{

    public function rules()
    {
        return [
            'new_email' => 'unique:users,email',
            'email' => 'unique:users,email',
            'username' => 'required'
        ];
    }

    public function filters()
    {
        return [
            'email' => 'lowercase'
        ];
    }

    public function sanitize(){
        parent::sanitize();

        //work on  replacing the emails
    }


}
