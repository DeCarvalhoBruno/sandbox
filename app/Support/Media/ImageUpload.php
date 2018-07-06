<?php namespace App\Support\Media;

class ImageUpload
{
    /**
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    protected $fileObject;
    protected $targetName;
    protected $targetSlug;
    protected $filename;
    protected $fileExtension;
    protected $path;
    protected $uuid;
    protected $targetType;
    protected $mediaType;
    protected $thumbnailFilename;
    protected $hddFilename;
    protected $hddPath;


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
        return $this->filename;
    }

    /**
     * @return null|string
     */
    public function getThumbnailFilename(): ?string
    {
        return $this->thumbnailFilename;
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