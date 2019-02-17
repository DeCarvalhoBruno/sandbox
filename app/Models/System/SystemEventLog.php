<?php namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class SystemEventLog extends Model {
    
    public $timestamps = false;
    protected $primaryKey = 'system_event_log_id';
    protected $table='system_event_log';
    
    protected $fillable = [
        'system_section_id',
        'system_event_id',
        'system_event_log_data',
        'created_at'
    ];

    
}
