<?php namespace App\Support\Media;

use App\Contracts\Image;
use App\Models\Media\Media;

class SimpleImage implements Image
{

    private $targetSlug;
    private $filename;
    private $hddFilename;
    private $uuid;
    private $fileExtension;
    private $targetType;

    /**
     *
     * @param $filename
     * @param $target
     * @param $type
     * @param $extension
     * @param $uuid
     */
    public function __construct($filename, $target, $type, $extension, $uuid)
    {
        $this->targetSlug = $target;
        $this->hddFilename = sprintf('%s.%s', $uuid, $extension);
        $this->filename = $filename;
        $this->fileExtension = $extension;

        $this->uuid = $uuid;
        $this->targetType = $type;
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


    public function getTargetSlug()
    {
        return $this->targetSlug;
    }

    /**
     * @return null|string
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getHddFilename(): string
    {
        return $this->hddFilename;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return mixed
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * @return mixed
     */
    public function getTargetType()
    {
        return $this->targetType;
    }

}