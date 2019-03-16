<?php namespace Naraki\Media\Providers;

use App\Support\Providers\Model;
use Naraki\Media\Contracts\Avatar as AvatarInterface;

/**
 * @method \Naraki\Media\Models\MediaType createModel(array $attributes = [])
 */
class Avatar extends Model implements AvatarInterface
{
    protected $model = \Naraki\Media\Models\MediaType::class;



}