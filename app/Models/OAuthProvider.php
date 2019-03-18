<?php

namespace App\Models;

use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OAuthProvider extends Model
{
    use DoesSqlStuff;

    protected $table = 'system_oauth_providers';
    protected $primaryKey = 'oauth_provider_id';
    protected $guarded = ['oauth_provider_id'];
    protected $hidden = [
        'access_token',
        'refresh_token',
    ];

    /**
     * @link https://laravel.com/docs/5.8/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeUser(Builder $builder)
    {
        return $this->joinReverse($builder, User::class);
    }
}
