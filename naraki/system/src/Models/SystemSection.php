<?php namespace Naraki\System\Models;

use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class SystemSection extends Model
{
    use Enumerable;

    const BACKEND = 1;
    const FRONTEND = 2;

    public $timestamps = false;
    protected $primaryKey = 'system_section_id';

    protected $fillable = [
        'system_section_name'
    ];

}
