<?php namespace App\Models\Media;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaEntity extends Model
{

    public $timestamps = false;
    protected $table = 'media_entities';
    protected $primaryKey = 'media_entity_id';
    protected $fillable = ['entity_type_id', 'media_category_record_id', 'media_entity_slug','media_entity_in_use'];

    /**
     * Sets the media as being used by a specific entity (user, forum thread etc.)
     * We call a stored procedure that sets all other media from that entity to "not used", because this method is called by entities
     * that can only use one media at a time (i.e profile picture in users, logo in owners or websites, etc.)
     *
     * @param $mediaEntityId
     */
    public static function setMediaAsUsed($mediaEntityId){
        $model = new static();
        $model->newQuery()->where($model->getKeyName(),$mediaEntityId)->update(['media_entity_in_use'=>true]);
        \DB::unprepared(sprintf('CALL sp_update_media_entity_in_use(%s)',$mediaEntityId));
    }

    public function getSlug(){
        return $this->getAttribute('media_entity_slug');
    }

}
