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
    /**
     * @var The type of user/feature the media is pertaining to, i.e. a user or a blog post
     * as defined in the Entity model
     * @var \App\Models\Entity
     */
    protected $targetType;
    /**
     * @var The type of media as defined in the media model class constants
     * @see \App\Models\Media\Media
     */
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
        return $this->hddFilename;
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