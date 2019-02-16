<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    public $table = 'people';
    public $primaryKey = 'person_id';

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'full_name',
        'user_id',
        'created_at'
    ];
    protected $hidden = [
        'person_id'
    ];

    /**
     * @param string $email
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildByEmail($email, $columns = ['*']): Builder
    {
        return Person::query()->select($columns)
            ->where('email', '=', $email);

    }


}
