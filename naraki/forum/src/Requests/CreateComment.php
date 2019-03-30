<?php namespace Naraki\Forum\Requests;

use App\Support\Requests\FormRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class CreateComment extends FormRequest
{
    /**
     * @var int
     */
    public static $characterLimit = 2000;
    /**
     * @var bool
     */
    protected $activateTagStrippingFilter = false;
    /**
     * @var array
     */
    private $mentions;

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
        $input = $this->input();
        preg_match_all('/(?<=\>\@)([a-z\_0-9]+)(?=\<)/', $input['txt'], $m);
        if (isset($m[0])) {
            $this->mentions = $m[0];
        }

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

    /**
     * @return array
     */
    public function getMentions()
    {
        return $this->mentions;
    }

    /**
     * @return bool
     */
    public function hasMentions()
    {
        return is_array($this->mentions);
    }

}