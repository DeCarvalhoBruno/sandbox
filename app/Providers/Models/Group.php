<?php namespace App\Providers\Models;

use App\Contracts\Models\Group as GroupInterface;

/**
 * @method \App\Models\Group createModel(array $attributes = [])
 * @method \App\Models\Group getOne($id, $columns = ['*'])
 */
class Group extends Model implements GroupInterface
{
    protected $model = \App\Models\Group::class;

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

    public function getMembers($groupName)
    {
        return $this->createModel()->newQuery()->select(['full_name','username'])->groupMember()->user()->where('group_name', '=', $groupName);

    }
}