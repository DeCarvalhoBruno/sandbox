<?php namespace Naraki\System\Providers;

use App\Support\Providers\Model;
use Naraki\System\Contracts\System as SystemInterface;
use Naraki\System\Contracts\EventLog as SystemEventLogInterface;
use Naraki\System\Contracts\UserSettings as SystemUserSettingsInterface;
use Naraki\System\Models\SystemEvent;

class System extends Model implements SystemInterface
{
    protected $model = \Naraki\System\Models\SystemEvent::class;

    /**
     * @var \Naraki\System\Contracts\EventLog
     */
    private $log;
    /**
     * @var \Naraki\System\Contracts\UserSettings|\Naraki\System\Providers\UserSettings
     */
    private $userSettings;

    /**
     *
     * @param \Naraki\System\Contracts\EventLog|\Naraki\System\Providers\EventLog $log
     * @param \Naraki\System\Contracts\UserSettings|\Naraki\System\Providers\UserSettings $ssi
     */
    public function __construct(SystemEventLogInterface $log, SystemUserSettingsInterface $ssi)
    {
        parent::__construct();
        $this->log = $log;
        $this->userSettings = $ssi;
    }

    /**
     * @return \Naraki\System\Contracts\EventLog|\Naraki\System\Providers\EventLog
     */
    public function log()
    {
        return $this->log;
    }

    /**
     * @return \Naraki\System\Contracts\UserSettings|\Naraki\System\Providers\UserSettings
     */
    public function userSettings()
    {
        return $this->userSettings;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEvents()
    {
        return SystemEvent::query()
            ->select(['system_event_id as id', 'system_event_name as name'])->get();
    }

}