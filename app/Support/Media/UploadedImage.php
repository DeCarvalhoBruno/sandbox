<?php namespace App\Support\Media;

use App\Contracts\Image;
use App\Models\Entity;
use App\Models\Media\Media;
use App\Models\Media\MediaEntity;
use Illuminate\Support\Collection;

class UploadedImage implements Image
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
    private $mediaType;
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
        $this->hddPath = sprintf('%s/media/%s/%s/', public_path(), $targetType, $mediaType);
        $this->targetType = Entity::getConstant($targetType);
        $this->mediaType = Media::getConstant($mediaType);
        $this->fileObject = $fileObject;
        $this->thumbnailFilename = ImageProcessor::makeFormatFilenameFromImageFilename(
            $this->hddFilename
        );
    }

    public function move()
    {
        $this->fileObject->move($this->hddPath, $this->hddFilename);
    }

    public function makeThumbnail()
    {

        $imageFullPath = $this->hddPath . $this->hddFilename;
        ImageProcessor::saveImg(
            ImageProcessor::makeCroppedImage($imageFullPath),
            media_entity_root_path(
                $this->targetType,
                $this->mediaType,
                $this->thumbnailFilename
            )
        );

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

    /**
     * @return mixed
     */
    public function getMediaType()
    {
        return $this->mediaType;
    }




}