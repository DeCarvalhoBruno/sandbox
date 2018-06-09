<?php namespace App\Support\Media;

use App\Contracts\Image;
use Laravolt\Avatar\Avatar;

class GeneratedAvatar implements Image
{
    private $targetName;
    private $targetSlugName;
    private $filename;
    private $path;

    /**
     *
     * @param $targetSlugname
     * @param $targetName
     * @param $targetType
     * @param $mediaType
     */
    public function __construct($targetSlugname, $targetName, $targetType, $mediaType)
    {
        $this->targetName = $targetName;
        $this->targetSlugName = $targetSlugname;
        $this->filename = makeFilename($targetName, 'png');
        $this->path = media_entity_root_path($targetType, $mediaType);
    }

    public function processAvatar()
    {
        $avatar = (new Avatar(app('config')->get('laravolt.avatar')))
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
    public function getTargetSlugName()
    {
        return $this->targetSlugName;
    }

    public function getOriginalFilename()
    {
        return null;
    }

    public function getNewFilename()
    {
        return $this->filename;
    }


}