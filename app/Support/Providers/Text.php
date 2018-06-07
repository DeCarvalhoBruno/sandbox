<?php namespace App\Support\Providers;

use App\Contracts\Models\Text as TextInterface;

/**
 * @method \App\Models\Media\MediaTypeTxt createModel(array $attributes = [])
 */
class Text extends Model implements TextInterface
{

    /**
     * @var string This provider's model class
     */
//    protected $model = \App\Models\Media\MediaTypeTxt::class;

    public function __construct($model = null)
    {
        parent::__construct($model);
    }
}