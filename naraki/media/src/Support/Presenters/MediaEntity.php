<?php namespace Naraki\Media\Support\Presenters;

use App\Support\Presenters\Presenter;

class MediaEntity extends Presenter
{
    public function asset()
    {
        return $this->entity->asset($this->entity->getAttribute('entity_id'),
                \Naraki\Media\Models\Media::IMAGE,
                \Naraki\Media\Models\MediaImgFormat::FEATURED);
    }

    public function thumbnail()
    {
        return $this->entity->asset($this->entity->getAttribute('entity_id'),
            \Naraki\Media\Models\Media::IMAGE,
            \Naraki\Media\Models\MediaImgFormat::THUMBNAIL);
    }

}