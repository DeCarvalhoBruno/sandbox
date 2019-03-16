<?php namespace Naraki\Media\Support;

use App\Contracts\Image;
use Naraki\Media\Models\Media;

class SimpleImage extends ImageUpload implements Image
{

    /**
     *
     * @param $filename
     * @param $target
     * @param $type
     * @param $mediaType
     * @param $extension
     * @param $uuid
     */
    public function __construct($filename, $target, $type, $mediaType, $extension, $uuid)
    {
        $this->targetSlug = $target;
        $this->hddFilename = sprintf('%s.%s', $uuid, $extension);
        $this->filename = $filename;
        $this->fileExtension = $extension;
        $this->uuid = $uuid;
        $this->targetType = $type;
        $this->mediaType = $mediaType;
    }


    /**
     * @param \stdClass $imageAlterations
     */
    public function cropAvatar($imageAlterations)
    {
        $imageFullPath = sprintf('%s/media/tmp/%s', public_path(), $this->hddFilename);
        ImageProcessor::saveImg(
            ImageProcessor::nipNTuck($imageFullPath, $imageAlterations),
            media_entity_root_path($this->targetType, Media::IMAGE_AVATAR, $this->hddFilename)
        );
        \File::delete($imageFullPath);

    }



}