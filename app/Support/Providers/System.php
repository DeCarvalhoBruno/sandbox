<?php namespace App\Support\Providers;

use App\Contracts\Models\System as SystemInterface;
use App\Contracts\Models\SystemEventLog as SystemEventLogInterface;

class System extends Model implements SystemInterface
{
    protected $model = \App\Models\System\SystemEvent::class;

    /**
     * @var \App\Contracts\Models\SystemEventLog
     */
    private $log;

    /**
     *
     * @param \App\Contracts\Models\SystemEventLog|\App\Support\Providers\SystemEventLog $log
     */
    public function __construct(SystemEventLogInterface $log)
    {
        parent::__construct();
        $this->log = $log;
    }

    public function log()
    {
        return $this->log;
    }

}