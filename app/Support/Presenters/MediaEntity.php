<?php namespace App\Support\Presenters;

class MediaEntity extends Presenter
{
    public function asset()
    {
        return $this->entity->asset($this->entity->getAttribute('entity_id'),
                \App\Models\Media\Media::IMAGE,
                \App\Models\Media\MediaImgFormat::FEATURED);
    }

    public function thumbnail()
    {
        return $this->entity->asset($this->entity->getAttribute('entity_id'),
            \App\Models\Media\Media::IMAGE,
            \App\Models\Media\MediaImgFormat::THUMBNAIL);
    }

}