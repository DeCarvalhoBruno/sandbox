<?php namespace Naraki\Forum\Requests;

use App\Support\Requests\FormRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

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

    public function afterValidation()
    {
        /**
         * @var $lastCommentDate \Carbon\Carbon
         */
        $lastCommentDate = Session::get('last_comment');
        if (!is_null($lastCommentDate)) {
            if ($lastCommentDate->addMinutes(2)->gt(Carbon::now())) {
                $this->getValidatorInstance()->errors()->add('_', trans('error.form.posting_delay'));
            }
        }
    }

}