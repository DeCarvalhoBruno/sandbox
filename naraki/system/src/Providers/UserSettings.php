<?php namespace Naraki\System\Providers;

use Naraki\Core\EloquentProvider;
use Naraki\System\Contracts\UserSettings as SystemInterface;
use Naraki\System\Models\SystemSection;

class UserSettings extends EloquentProvider implements SystemInterface
{
    protected $model = \Naraki\System\Models\SystemUserSettings::class;

    /**
     * @param int $userID
     * @param int $systemSectionID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getSettings($userID, $systemSectionID = SystemSection::BACKEND)
    {
        return $this->build()
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
            $events = implode(',', $data['events']);
            if (empty($events)) {
                $events = null;
            }
            $existing = $this->build()
                ->where('user_id', '=', $userId)
                ->first();
            if (!is_null($existing)) {
                $existing->update(['system_events_subscribed' => $events]);
            } else {
                $this->createModel([
                    'user_id' => $userId,
                    'system_section_id' => $systemSectionID,
                    'system_events_subscribed' => $events
                ])->save();
            }
        }
    }

}