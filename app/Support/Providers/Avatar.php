<?php namespace App\Support\Providers;

use App\Contracts\Models\Avatar as AvatarInterface;
use App\Models\Entity;

/**
 * @method \App\Models\Media\MediaType createModel(array $attributes = [])
 */
class Avatar extends Model implements AvatarInterface
{
    protected $model = \App\Models\Media\MediaType::class;



}