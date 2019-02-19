<?php

namespace App\Models;

use App\Traits\Models\HasASlugColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasASlugColumn;

    public $table = 'people';
    public $primaryKey = 'person_id';
    public static $slugColumn = 'person_slug';

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'full_name',
        'user_id',
        'person_slug',
        'created_at'
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
