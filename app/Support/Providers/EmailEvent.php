<?php namespace App\Services\Email;

use App\Support\Providers\Model;

/**
 * @method \App\Models\Email\EmailEvent createModel(array $attributes = [])
 */
class EmailEvent extends Model
{
    protected $model = \App\Models\Email\EmailEvent::class;

    public function getDefaults()
    {
        return $this->getAll();
    }
}