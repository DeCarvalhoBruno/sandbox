<?php namespace App\Support\Media;

use App\Exceptions\DiskFolderNotFoundException;
use App\Models\Media\MediaTypeImgFormat;

class Media
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private $fileObject;
    private $originalName;
    private $newName;
    private $newPath;
    private $newFullPath;
    private $targetName;

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
        $this->originalName = $fileObject->getClientOriginalName();
        $this->targetName = $targetName;
        $this->newName = makeFilename($targetName, $fileObject->getClientOriginalExtension());
        $this->newPath = media_entity_root_path($targetType, $mediaType);
        $this->newFullPath=$this->newPath.$this->newName;
        if (!is_readable($this->newPath)) {
            throw new DiskFolderNotFoundException(sprintf('Cannot write into %s', $this->newPath));
        }
        $this->fileObject = $fileObject;
    }

    public function move()
    {
        $this->fileObject->move($this->newPath, $this->newName);
    }

    public function processAvatar()
    {
        $image = Image::makeCroppedImage($this->fileObject->getRealPath(),MediaTypeImgFormat::THUMBNAIL);
        Image::saveImg($image,$this->newFullPath);
    }

    /**
     * Get the name of the resource after which this media is named.
     * I.e John Doe's avatar picture 'target name' is his username, john_doe
     */
    public function getTargetName()
    {
        return $this->targetName;
    }

    /**
     * @return null|string
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }

}