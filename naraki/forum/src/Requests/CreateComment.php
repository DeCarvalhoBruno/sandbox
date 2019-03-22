<?php namespace Naraki\Forum\Requests;

use App\Support\Requests\FormRequest;

class CreateComment extends FormRequest
{
    public static $characterLimit = 2000;
    protected $activateTagStrippingFilter = false;

    public function rules()
    {
        return [
            'txt' => 'required|max:' . static::$characterLimit,
        ];
    }

    public function filters()
    {
        return [
            'txt' => 'purify',
        ];
    }

}