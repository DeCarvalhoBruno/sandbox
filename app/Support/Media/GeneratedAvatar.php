<?php namespace App\Support\Media;

use App\Contracts\Image;
use Laravolt\Avatar\Avatar;

class GeneratedAvatar extends ImageUpload implements Image
{
    protected $fileExtension = 'png';

    /**
     *
     * @param $targetSlug
     * @param $targetName
     * @param $targetType
     * @param $mediaType
     */
    public function __construct($targetSlug, $targetName, $targetType, $mediaType)
    {
        $this->targetName = $targetName;
        $this->targetSlug = $targetSlug;
        $this->uuid = makeHexUuid();
        $this->filename = sprintf('%s.%s', $this->uuid, $this->fileExtension);
        $this->path = media_entity_root_path($targetType, $mediaType);
        $this->targetType = $targetType;
        $this->mediaType = $mediaType;
    }

    public function processAvatar()
    {
        (new Avatar(app('config')->get('laravolt.avatar')))
            ->create(strtoupper($this->targetName))
            ->save($this->path . $this->filename);
    }


}