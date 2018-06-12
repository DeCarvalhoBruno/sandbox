<?php namespace App\Support\Providers;

use App\Contracts\Image as ImageContract;
use App\Contracts\Models\Avatar as AvatarInterface;
use App\Models\Entity;
use App\Models\EntityType;

/**
 * @method \App\Models\Media\MediaType createModel(array $attributes = [])
 */
class Avatar extends Model implements AvatarInterface
{
    protected $model = \App\Models\Media\MediaType::class;

    /**
     * @param $entityId
     * @param \App\Contracts\Image $image
     * @return int
     * @throws \Exception
     */
    public function save($entityId, ImageContract $image)
    {
        $targetEntityTypeId = EntityType::getEntityTypeID($entityId, $image->getTargetSlug());
        if (!is_int($targetEntityTypeId)) {
            throw new \UnexpectedValueException('Entity could not be found for image saving');
        }
        return $targetEntityTypeId;
    }

    public static function setAsUsed($uuid)
    {
        if (strlen($uuid) == 32 && ctype_xdigit($uuid)) {
            return \DB::unprepared(sprintf('CALL sp_update_media_type_in_use("%s")', $uuid));
        }
        throw new \UnexpectedValueException('uuid is not valid');
    }

    public function delete($uuid)
    {
        if (strlen($uuid) == 32 && ctype_xdigit($uuid)) {
            $media = $this->createModel()->newQuery()->select([
                'media_types.media_type_id',
                'media_uuid',
                'media_extension'
            ])
                ->where('media_uuid', '=', $uuid)->mediaDigital()->first();
            if (!is_null($media)) {
                @\File::delete(media_entity_root_path(
                    Entity::USERS,
                    \App\Models\Media\Media::IMAGE_AVATAR,
                    $media->getFilename())
                );
                return $media->delete();
            }
            throw new \UnexpectedValueException('Media was not found');
        }
        throw new \UnexpectedValueException('uuid is not valid');
    }

}