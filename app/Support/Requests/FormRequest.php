<?php

namespace App\Support\Requests;

use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class FormRequest extends LaravelFormRequest
{
    /**
     *  Sanitize input before validating.
     *
     *  @return void
     */
    public function validate()
    {
        $this->sanitize();
        parent::validate();
    }

    /**
     *  Sanitize this request's input
     *
     *  @return void
     */
    public function sanitize()
    {
        $this->sanitizer = new Sanitizer($this->input(), $this->filters());
        $this->replace($this->sanitizer->sanitize());
    }

   /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
