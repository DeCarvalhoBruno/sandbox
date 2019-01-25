<?php

namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;

class UpdateMedia extends FormRequest
{
    public function rules()
    {
        return [
            'media_title' => 'between:1,255',
            'media_alt' => 'max:255',
        ];
    }

    public function filters()
    {
        return [
            'media_title' => 'strip_tags',
            'media_alt' => 'strip_tags',
            'media_description' => 'purify',
            'media_caption' => 'purify'
        ];
    }
}
