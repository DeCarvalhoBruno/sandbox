<?php namespace App\Support\Providers;

use App\Contracts\Models\SystemUserSettings as SystemInterface;
use App\Models\System\SystemSection;

class SystemUserSettings extends Model implements SystemInterface
{
    protected $model = \App\Models\System\SystemUserSettings::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param int $userID
     * @param int $systemSectionID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getSettings($userID, $systemSectionID = SystemSection::BACKEND)
    {
        return $this->createModel()->newQuery()
            ->where('user_id', '=', $userID)
            ->where('system_section_id', '=', $systemSectionID);
    }

    /**
     * @param int $userId
     * @param int $systemSectionID
     * @param array $data
     */
    public function save($userId, $systemSectionID, $data)
    {
        if (isset($data['events']) && is_array($data['events'])) {
            sort($data['events']);
            $existing = $this->createModel()->newQuery()
                ->where('user_id', '=', $userId)
                ->first();
            if (!is_null($existing)) {
                $existing->update(['system_events_subscribed' => implode(',', $data['events'])]);
            } else {
                $this->createModel([
                    'user_id' => $userId,
                    'system_section_id' => $systemSectionID,
                    'system_events_subscribed' => implode(',', $data['events'])
                ])->save();
            }
        }
    }

}