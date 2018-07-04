<?php namespace App\Support\Providers;

use App\Contracts\Models\Avatar as AvatarInterface;
use App\Models\Entity;

/**
 * @method \App\Models\Media\MediaType createModel(array $attributes = [])
 */
class Avatar extends Model implements AvatarInterface
{
    protected $model = \App\Models\Media\MediaType::class;

    public static function setAsUsed($uuid)
    {
        if (is_hex_uuid_string($uuid)) {
            return \DB::unprepared(sprintf('CALL sp_update_media_type_in_use("%s")', $uuid));
        }
        throw new \UnexpectedValueException('uuid is not valid');
    }

    public function delete($uuid)
    {
        if (is_hex_uuid_string($uuid)) {
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