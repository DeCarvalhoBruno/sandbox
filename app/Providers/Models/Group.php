<?php namespace App\Providers\Models;

use App\Contracts\Models\Group as GroupInterface;
use App\Support\MysqlRawQueries;

/**
 * @method \App\Models\Group createModel(array $attributes = [])
 * @method \App\Models\Group getOne($id, $columns = ['*'])
 */
class Group extends Model implements GroupInterface
{
    protected $model = \App\Models\Group::class;

    /**
     * @param string $groupName
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getOneByName($groupName, $columns = ['*'])
    {
        return $this->createModel()->newQuery()->select($columns)->where('group_name', '=', $groupName);
    }

    /**
     * @param string $groupName
     * @param array $data
     * @return int
     */
    public function updateOneByName($groupName, $data)
    {
        return $this->updateOneGroup($this->createModel(), 'group_name', $groupName, $data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return int
     */
    public function updateOneById($id, $data)
    {
        $model = $this->createModel();
        return $this->updateOneGroup($model, $model->getKeyName(), $id, $data);
    }

    /**
     * @param \App\Models\Group $model
     * @param string $field
     * @param string $value
     * @param array $data
     * @return int
     */
    public function updateOneGroup($model, $field, $value, $data)
    {
        return $model->newQuery()->where($field, $value)
            ->update($this->filterFillables($data));
    }

    /**
     * @param $groupName
     * @return array
     */
    public function getMembers($groupName)
    {
        $model = $this->createModel();
        $count = $model->newQuery()->select(\DB::raw('count(group_members.user_id) as c'))->groupMember()->user()->where('group_name',
            '=', $groupName)->pluck('c')->pop();
        if ($count > 25) {
            return ['count' => $count];
        } else {
            return [
                'count' => $count,
                'users' =>
                    $this->createModel()->newQuery()->select(['full_name as text', 'username as id'])
                        ->groupMember()->user()->where('group_name', '=', $groupName)
                        ->orderBy('last_name', 'asc')->get()
            ];
        }
    }

    /**
     * @param string $groupName
     * @param string $search
     * @return \App\Models\Group[]
     */
    public function searchMembers($groupName, $search)
    {
        return $this->createModel()->newQuery()->select(['full_name as text', 'username as id'])
            ->groupMember()->user()->where('group_name', '=', $groupName)
            ->where('full_name', 'like', sprintf('%%%s%%', $search))
            ->get();
    }

    public function updateMembers($groupName, $data)
    {
        $model = $this->createModel();
        if (!is_null($data->added)) {
            $user = new \App\Models\User();
//            $userIds = $user->newQueryWithoutScopes()->select(['user_id'])->whereIn('username',
//                $data->added)->pluck('user_id');
            $userIds = MysqlRawQueries::getUsersInArrayNotInGroup($data->added, $groupName);

        }

        if (!is_null($data->removed)) {

        }

    }
}