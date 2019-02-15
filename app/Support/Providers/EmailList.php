<?php namespace App\Support\Providers;

use App\Contracts\Models\EmailList as EmailListInterface;
use App\Models\Email\EmailList as EmailListModel;

/**
 * @method \App\Models\Email\EmailList createModel(array $attributes = [])
 */
class EmailList extends Model implements EmailListInterface
{
    protected $model = \App\Models\Email\EmailList::class;

    public function getList()
    {
        return EmailListModel::getList();
    }
}