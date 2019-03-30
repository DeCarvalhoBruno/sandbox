<?php namespace Naraki\Media\Support\Presenters;

use App\Support\Presenters\Presenter;
use Naraki\Media\Models\Media;
use Naraki\Media\Models\MediaImgFormat;

/**
 * @see \Naraki\Media\Models\MediaEntity
 */
class MediaEntity extends Presenter
{
    public function asset($media = Media::IMAGE, $format = MediaImgFormat::FEATURED)
    {
        return $this->entity->asset(
            $this->entity->getAttribute('entity_id'),
            $media,
            $format
        );
    }

    public function thumbnail($media = Media::IMAGE, $format = MediaImgFormat::FEATURED)
    {
        return $this->entity->asset(
            $this->entity->getAttribute('entity_id'),
            $media,
            $format
        );
    }

}