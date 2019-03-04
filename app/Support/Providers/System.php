<?php namespace App\Support\Providers;

use App\Contracts\Models\System as SystemInterface;
use App\Contracts\Models\SystemEventLog as SystemEventLogInterface;
use App\Contracts\Models\SystemUserSettings as SystemUserSettingsInterface;
use App\Models\System\SystemEvent;

class System extends Model implements SystemInterface
{
    protected $model = \App\Models\System\SystemEvent::class;

    /**
     * @var \App\Contracts\Models\SystemEventLog
     */
    private $log;
    /**
     * @var \App\Models\System\SystemUserSettings|\App\Support\Providers\SystemUserSettings
     */
    private $userSettings;
    /**
     * @var \App\Models\System\SystemUserSettings|\App\Support\Providers\SystemUserSettings
     */
    private $settings;

    /**
     *
     * @param \App\Contracts\Models\SystemEventLog|\App\Support\Providers\SystemEventLog $log
     * @param \App\Contracts\Models\SystemUserSettings|\App\Support\Providers\SystemUserSettings $ssi
     * @param \App\Support\Providers\SystemSettings $ss
     */
    public function __construct(SystemEventLogInterface $log, SystemUserSettingsInterface $ssi, SystemSettings $ss)
    {
        parent::__construct();
        $this->log = $log;
        $this->userSettings = $ssi;
        $this->settings = $ss;
    }

    public function log()
    {
        return $this->log;
    }

    public function userSettings()
    {
        return $this->userSettings;
    }

    public function settings()
    {
        return $this->settings;
    }

    public function getEvents(){
        return SystemEvent::query()
            ->select(['system_event_id as id','system_event_name as name'])->get();
    }

}