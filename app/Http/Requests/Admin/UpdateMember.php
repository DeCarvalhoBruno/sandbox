<?php

namespace App\Http\Requests\Admin;

use App\Support\Requests\FormRequest;

class UpdateMember extends FormRequest
{
    public function rules()
    {
        return [];
    }

    public function afterValidation()
    {
        $added = $this->extractUsers($this->input('added'));
        $removed = $this->extractUsers($this->input('removed'));
        $this->replace(['added' => $added, 'removed' => $removed]);
    }

    private function extractUsers($data)
    {
        return (!is_null($data)) ? array_filter(array_map(function ($v) {
            return isset($v['id']) ? $v['id'] : null;
        }, $data)) : null;
    }
}
