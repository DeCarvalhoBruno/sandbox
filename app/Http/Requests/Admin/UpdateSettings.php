<?php namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;

class UpdateSettings extends FormRequest
{
    public function afterValidation()
    {
        $input = $this->input();
        $input['jsonld'] = !isset($input['jsonld']) ? false : true;
        $input['robots'] = !isset($input['robots']) ? false : true;

        $this->replace($input);
    }

}