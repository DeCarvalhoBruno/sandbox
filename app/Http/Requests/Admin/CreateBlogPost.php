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
    private $personSlug;
    /**
     * @var array
     */
    private $categories = [];
    /**
     * @var array
     */
    private $tags=[];

    public function rules()
    {
        return [
            'blog_post_title' => 'max:255',
            'blog_post_status' => 'status',
            'published_at' => 'date_format:YmdHi'
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

        if (isset($input['blog_post_person'])) {
            $this->personSlug = $input['blog_post_person'];
            unset($input['blog_post_person']);
        }

        if (isset($input['categories'])) {
            $this->categories = $input['categories'];
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

        if (isset($input['published_at'])) {
            //Taking in a date format which we set manually in javascript.
            // This ensures we get a consistent format we can convert easily as opposed to locale based date formats
            $input['published_at'] = date_create_from_format('YmdHi', $input['published_at']);
        }

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
    public function getPersonSlug()
    {
        return $this->personSlug;
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

    public function setPersonSlug($id)
    {
        $this->merge(['person_id'=>$id]);
    }


}
