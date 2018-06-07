<?php namespace App\Support\Providers;

use App\Contracts\Models\Media as MediaInterface;
use App\Contracts\Models\File as FileInterface;

/**
 * @method \App\Models\Media\Media createModel(array $attributes = [])
 */
class Media extends Model implements MediaInterface
{
    /**
     * @var \App\Contracts\Models\File|\App\Support\Providers\File
     */
    protected $file;
    
    /**
     * @var string This provider's model class
     */
    protected $model = \App\Models\Media\Media::class;

    /**
     * Media constructor.
     *
     * @param \App\Contracts\Models\File|\App\Support\Providers\File $fi
     * @param string|null $model
     */
    public function __construct(FileInterface $fi, $model = null)
    {
        parent::__construct($model);
        $this->file          = $fi;
    }

    /**
     * @return \App\Contracts\Models\File|\App\Support\Providers\File
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * @return \App\Contracts\Models\Image
     */
    public function image()
    {
        return $this->file->image();
    }

    /**
     * @return \App\Contracts\Models\Text
     */
    public function text()
    {
        return $this->file->text();
    }
    

    /**
     * @param int $mediaId
     *
     * @return string
     */
    public static function getSupportedFileFormats($mediaId)
    {
        switch ($mediaId) {
            default:
                return 'JPG, PNG';
                break;
        }
    }
}