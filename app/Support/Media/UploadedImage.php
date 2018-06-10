<?php namespace App\Support\Media;

use App\Contracts\Image;
use App\Exceptions\DiskFolderNotFoundException;

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

    /**
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $fileObject
     * @param string $targetName
     * @param $targetType
     * @param $mediaType
     * @throws \App\Exceptions\DiskFolderNotFoundException
     */
    public function __construct($fileObject, $targetName, $targetType, $mediaType)
    {
        $this->filename = $fileObject->getClientOriginalName();
        $this->targetSlug = $targetName;
        $this->uuid = makeHexUuid();
        $this->fileExtension = $fileObject->getClientOriginalExtension();
        $this->hddFilename = sprintf('%s.%s', $this->uuid, $this->fileExtension);
        $this->hddPath = media_entity_root_path($targetType, $mediaType);
        if (!is_readable($this->hddPath)) {
            throw new DiskFolderNotFoundException(sprintf('Cannot write into %s', $this->hddPath));
        }
        $this->fileObject = $fileObject;
    }

    public function move()
    {
        $this->fileObject->move($this->hddPath, $this->hddFilename);
    }

    public function processAvatar()
    {
        $this->move();
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


}