<?php namespace Naraki\Mail\Providers;

use App\Support\Providers\Model;
use Naraki\Mail\Contracts\Listing as EmailListInterface;
use Naraki\Mail\Models\EmailList as EmailListModel;

/**
 * @method \Naraki\Mail\Models\EmailList createModel(array $attributes = [])
 */
class Listing extends Model implements EmailListInterface
{
    protected $model = \Naraki\Mail\Models\EmailList::class;

    public function getList()
    {
        return EmailListModel::getList();
    }
}