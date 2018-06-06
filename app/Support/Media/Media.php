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

}