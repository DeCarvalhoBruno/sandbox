<?php namespace Naraki\Media\Models;

use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MediaDigital extends Model
{
    use DoesSqlStuff;

    const MEDIA_DIGITAL_MAX_FILESIZE = 2;

    protected $table = 'media_digital';
    protected $primaryKey = 'media_digital_id';
    protected $fillable = ['media_type_id', 'media_filename', 'media_extension', 'media_alt', 'media_caption'];
    protected $hidden = ['media_digital_id', 'media_type_id'];

    /**
     * @param string $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(get_locale_date_format());
    }

    /**
     * @link https://laravel.com/docs/5.8/eloquent#local-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeMediaType(Builder $builder)
    {
        return $this->joinReverse($builder, MediaType::class);
    }

}