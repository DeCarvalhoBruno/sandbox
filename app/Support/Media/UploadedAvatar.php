<?php namespace App\Support\Media;

use App\Contracts\Image;
use App\Models\Entity;
use Illuminate\Support\Collection;

class UploadedAvatar implements Image
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private $fileObject;
    private $filename;
    private $fileExtension;
    private $hddFilename;
    private $hddPath;
    private $targetSlug;
    private $uuid;
    private $targetType;
    private $thumbnailFilename;

    /**
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $fileObject
     * @param string $targetName
     * @param $targetType
     * @param $mediaType
     */
    public function __construct($fileObject, $targetName, $targetType, $mediaType)
    {
        $this->filename = $fileObject->getClientOriginalName();
        $this->targetSlug = $targetName;
        $this->uuid = makeHexUuid();
        $this->fileExtension = $fileObject->getClientOriginalExtension();
        $this->hddFilename = sprintf('%s.%s', $this->uuid, $this->fileExtension);
//        $this->hddPath = media_entity_root_path(Entity::getConstant($targetType), Media::getConstant($mediaType));
        $this->hddPath = sprintf('%s/media/tmp/', public_path());
        $this->fileObject = $fileObject;
        $this->targetType = Entity::getConstant($targetType);
    }

    public function move()
    {
        $this->fileObject->move($this->hddPath, $this->hddFilename);
    }

    public function saveTemporaryAvatar()
    {
        $this->move();
        if (\Cache::has('temporary_avatars')) {
            $data = \Cache::get('temporary_avatars');
        } else {
            $data = new Collection();
        }
        $data->put($this->uuid,
            new SimpleImage(
                $this->filename,
                $this->targetSlug,
                $this->targetType,
                $this->fileExtension,
                $this->uuid
            )
        );
        \Cache::forever('temporary_avatars', $data);
    }

    public function processImage()
    {
        //$image = Image::makeCroppedImage($this->fileObject->getRealPath(),MediaTypeImgFormat::THUMBNAIL);
        //Image::saveImg($image,$this->newFullPath);
    }

    /**
     * Get the name of the resource after which this media is named.
     * I.e John Doe's avatar picture 'target name' is his username, john_doe
     */
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
     * @return null|string
     */
    public function getThumbnailFilename(): ?string
    {
        return $this->thumbnailFilename;
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