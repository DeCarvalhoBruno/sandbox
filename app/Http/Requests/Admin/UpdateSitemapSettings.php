<?php namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;

class UpdateSitemapSettings extends FormRequest
{
    public function afterValidation()
    {
        $input = $this->input();
        $input['sitemap'] = !isset($input['sitemap']) ? false : $input['sitemap']=="true";

        $this->replace($input);
    }

}