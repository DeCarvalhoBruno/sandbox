<?php namespace App\Support\Media;

use App\Contracts\Image;
use App\Exceptions\DiskFolderNotFoundException;

class UploadedImage implements Image
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private $fileObject;
    private $originalFilename;
    private $newFilename;
    private $newPath;
    private $newFullPath;
    private $targetSlugName;

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
        $this->originalFilename = $fileObject->getClientOriginalName();
        $this->targetSlugName = $targetName;
        $this->newFilename = makeFilename($targetName, $fileObject->getClientOriginalExtension());
        $this->newPath = media_entity_root_path($targetType, $mediaType);
        $this->newFullPath=$this->newPath.$this->newFilename;
        if (!is_readable($this->newPath)) {
            throw new DiskFolderNotFoundException(sprintf('Cannot write into %s', $this->newPath));
        }
        $this->fileObject = $fileObject;
    }

    public function move()
    {
        $this->fileObject->move($this->newPath, $this->newFilename);
    }

    public function processAvatar()
    {
        $this->fileObject->move($this->newPath, $this->newFilename);
//        $image = Image::makeCroppedImage($this->fileObject->getRealPath(),MediaTypeImgFormat::THUMBNAIL);
//        Image::saveImg($image,$this->newFullPath);
    }

    /**
     * Get the name of the resource after which this media is named.
     * I.e John Doe's avatar picture 'target name' is his username, john_doe
     */
    public function getTargetSlugName()
    {
        return $this->targetSlugName;
    }

    /**
     * @return null|string
     */
    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    /**
     * @return string
     */
    public function getNewFilename(): string
    {
        return $this->newFilename;
    }

}