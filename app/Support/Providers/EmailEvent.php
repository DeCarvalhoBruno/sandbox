<?php namespace App\Support\Providers;

use App\Contracts\Models\EmailEvent as EmailEventInterface;

/**
 * @method \App\Models\Email\EmailEvent createModel(array $attributes = [])
 */
class EmailEvent extends Model implements EmailEventInterface
{
    protected $model = \App\Models\Email\EmailEvent::class;

    public function getDefaults()
    {
        return $this->getAll();
    }
}