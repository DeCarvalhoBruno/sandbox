<?php namespace Naraki\Media\Providers;

use App\Support\Providers\Model;
use Naraki\Media\Contracts\Media as MediaInterface;
use Naraki\Media\Contracts\File as FileInterface;

/**
 * @method \Naraki\Media\Models\Media createModel(array $attributes = [])
 */
class Media extends Model implements MediaInterface
{
    /**
     * @var \Naraki\Media\Contracts\File|\Naraki\Media\Providers\File
     */
    protected $file;
    
    /**
     * @var string This provider's model class
     */
    protected $model = \Naraki\Media\Models\Media::class;

    /**
     * Media constructor.
     *
     * @param \Naraki\Media\Contracts\File|\Naraki\Media\Providers\File $fi
     * @param string|null $model
     */
    public function __construct(FileInterface $fi, $model = null)
    {
        parent::__construct($model);
        $this->file          = $fi;
    }

    /**
     * @return \Naraki\Media\Contracts\File|\Naraki\Media\Providers\File
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * @return \Naraki\Media\Contracts\Image|\Naraki\Media\Providers\Image
     */
    public function image()
    {
        return $this->file->image();
    }

    /**
     * @return \Naraki\Media\Contracts\Text
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