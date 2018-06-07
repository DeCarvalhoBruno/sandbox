<?php namespace App\Support\Providers;

use App\Contracts\Models\File as FileInterface;
use App\Contracts\Models\Image as ImageInterface;
use App\Contracts\Models\Text as TextInterface;

/**
 * @method \App\Models\Media\MediaType createModel(array $attributes = [])
 */
class File extends Model implements FileInterface
{
    /**
     * @var string This provider's model class
     */
    protected $model = \App\Models\Media\MediaTypeImg::class;

    /**
     * @var \App\Contracts\Models\Image|\App\Support\Providers\Image
     */
    protected $image;

    /**
     * @var \App\Contracts\Models\Text|\App\Support\Providers\Text
     */
    protected $text;

    /**
     * File constructor.
     *
     * @param \App\Contracts\Models\Image|\App\Support\Providers\Image $i
     * @param \App\Contracts\Models\Text|\App\Support\Providers\Text $t
     * @param null $model
     */
    public function __construct(ImageInterface $i, TextInterface $t, $model = null)
    {
        parent::__construct($model);
        $this->image = $i;
        $this->text  = $t;
    }

    /**
     * @return \App\Contracts\Models\Image|\App\Support\Providers\Image
     */
    public function image()
    {
        return $this->image;
    }

    /**
     * @return \App\Contracts\Models\Text|\App\Support\Providers\Text
     */
    public function text()
    {
        return $this->text;
    }

}