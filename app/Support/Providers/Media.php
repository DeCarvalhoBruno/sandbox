<?php namespace App\Support\Providers;

use App\Contracts\Models\Media as MediaInterface;
use App\Models\EntityType;

/**
 * @method \App\Models\Media\Media createModel(array $attributes = [])
 */
class Media extends Model implements MediaInterface
{
    /**
     * @var string This provider's model class
     */
    protected $model = \App\Models\Media\Media::class;

    public function saveAvatar($entityId, $target)
    {
        $targetEntityTypeId = EntityType::getEntityTypeID($entityId, $target);


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