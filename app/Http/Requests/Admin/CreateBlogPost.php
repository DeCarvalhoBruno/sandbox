<?php

namespace App\Http\Requests\Admin;

use App\Models\Blog\BlogPostStatus;
use App\Support\Requests\FormRequest;
use Illuminate\Support\Facades\Validator;

class CreateBlogPost extends FormRequest
{
    protected $activateTagStrippingFilter = false;

    private $username;

    public function rules()
    {
        return [
            'blog_post_title' => 'max:255',
            'blog_post_status' => 'status',
        ];
    }

    public function filters()
    {
        return [
            'blog_post_title' => 'strip_tags',
            'blog_post_content' => 'purify'
        ];
    }

    public function afterValidation()
    {
        $input = $this->input();
        $this->username = $input['blog_post_user'];
        unset($input['blog_post_user']);

        if (isset($input['blog_post_status'])) {
            $input['blog_post_status_id'] = BlogPostStatus::getConstant($input['blog_post_status']);
            unset($input['blog_post_status']);
            $this->replace($input);
        }
    }

    public function prepareForValidation()
    {
        Validator::extend('status', function ($attribute, $value, $parameters, $validator) {
            return BlogPostStatus::isValidName($value);
        });
        parent::prepareForValidation();
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }



}
