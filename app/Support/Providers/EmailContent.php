<?php namespace App\Services\Email;

use App\Contracts\Models\Email as EmailInterface;
use App\Models\Email\EmailEvent as EmailEventModel;
use App\Models\Email\EmailRecipientType;
use App\Support\Providers\Model;
use Illuminate\Support\Str;

/**
 * @method \App\Models\Email\Email createModel(array $attributes = [])
 */
class EmailContent extends Model implements EmailInterface
{
    protected $model = \App\Models\Email\Email::class;


    /**
     * Gets content to be displayed in emails. Calls the appropriate function based
     * on the type of email being sent.
     *
     * @param int $targetID
     * @param int $emailEventID
     *
     * @return mixed
     * @throws \Exception
     */
    public function yieldEmailContent($targetID, $emailEventID = null)
    {
        $collection = call_user_func(
            [$this, Str::camel(EmailEventModel::getConstantName($emailEventID))],
            $targetID
        );
        if ( ! $collection->has('recipient_type')) {
            $collection->put('recipient_type', EmailRecipientType::ALL);
        }

        return $collection;
    }

}