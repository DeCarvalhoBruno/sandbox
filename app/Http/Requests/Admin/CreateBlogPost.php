<?php

namespace App\Http\Requests\Admin;

use App\Models\Blog\BlogPostStatus;
use App\Support\Requests\FormRequest;
use Illuminate\Support\Facades\Validator;

class CreateBlogPost extends FormRequest
{
    protected $activateTagStrippingFilter = false;

    /**
     * @var string
     */
    private $username;
    /**
     * @var array
     */
    private $categories = [];
    /**
     * @var array
     */
    private $tags;

    public function rules()
    {
        return [
            'blog_post_title' => 'max:255',
            'blog_post_status' => 'status',
            'published_at'=>'date_format:YmdHi'
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

        if (isset($input['categories'])) {
            $this->categories = array_filter(
                $input['categories'],
                function ($val) {
                    return is_hex_uuid_string($val);
                }
            );
            unset($input['categories']);
        }

        if (isset($input['tags'])) {
            $this->tags = array_unique($input['tags']);
            unset($input['tags']);
        }

        if (isset($input['blog_post_status'])) {
            $input['blog_post_status_id'] = BlogPostStatus::getConstant($input['blog_post_status']);
            unset($input['blog_post_status']);
        }

        //Taking in a date format which we set manually in javascript to avoid weirdness with locale based date formats
        $input['published_at'] = date_create_from_format('YmdHi', $input['published_at']);

        $this->replace($input);
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

    /**
     * @return mixed
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }


}
