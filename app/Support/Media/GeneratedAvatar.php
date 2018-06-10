<?php namespace App\Support\Media;

use App\Contracts\Image;
use Laravolt\Avatar\Avatar;

class GeneratedAvatar implements Image
{
    private $targetName;
    private $targetSlug;
    private $filename;
    private $fileExtension='png';
    private $path;
    private $uuid;

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
        $this->filename = sprintf('%s.%s',$this->uuid,$this->fileExtension);
        $this->path = media_entity_root_path($targetType, $mediaType);
    }

    public function processAvatar()
    {
        (new Avatar(app('config')->get('laravolt.avatar')))
            ->create(strtoupper($this->targetName))
            ->save($this->path . $this->filename);
    }

    /**
     * @return mixed
     */
    public function getTargetName()
    {
        return $this->targetName;
    }

    /**
     * @return mixed
     */
    public function getTargetSlug()
    {
        return $this->targetSlug;
    }

    public function getFilename()
    {
        return null;
    }

    public function getHddFilename()
    {
        return $this->filename;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }




}