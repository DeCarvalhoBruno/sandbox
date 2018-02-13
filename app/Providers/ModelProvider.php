<?php namespace App\Providers;


abstract class ModelProvider
{

    /**
     * The Eloquent user model.
     *
     * @var string
     */
    protected $model;

    /**
     * ModelProvider constructor.
     *
     * @param string $model
     */
    public function __construct($model = null)
    {
        if ( ! empty($model)) {
            $this->model = $model;
        }
    }

    /**
     * Create a new instance of the model.
     * @see Model::__construct
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createModel(array $attributes = [])
    {
        $class = '\\' . ltrim($this->model, '\\');

        if (class_exists($class)) {
            return new $class($attributes);
        }
        throw new \UnexpectedValueException('Model class could not be found or class variable has not been initialized.');
    }

    /**
     * Gets the name of the Eloquent model.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll($columns = ['*'])
    {
        $model = $this->createModel();

        return $model->newQuery()
                     ->where($model->getKeyName(), '>', 0)
                     ->get($columns);
    }

    /**
     * @param $id
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getOne($id, $columns = ['*'])
    {
        return $this->createModel()->find($id, $columns);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildOne($id)
    {
        $model = $this->createModel();

        return $model->where(sprintf('%s.%s', $model->getTable(), $model->getKeyName()), $id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function build()
    {
        return $this->createModel();
    }

    /**
     * @param $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createOne($data)
    {
        return $this->createModel()->create($data);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return int
     */
    public function updateOne($id, $data)
    {
        $model = $this->createModel();

        return $model->where($model->getKeyName(), $id)->update($data);
    }

    /**
     * @param int $id
     *
     * @return int
     */
    public function deleteOne($id)
    {
        $model = $this->createModel();

        return $model->where($model->getKeyName(), $id)->delete();
    }

    /**
     * For models using the HasASystemEntity trait
     * 
     * @see \App\Support\Traits\HasASystemEntity
     * @param int $id
     * @param int $systemEntityID
     *
     * @return mixed
     */
    public function deleteWithMedia($id,$systemEntityID){
        $model = $this->createModel();

        return $model->deleteWithMedia($id,$systemEntityID);
    }

    /**
     * @param array $ids
     * @param int $systemEntityID
     */
    public function deleteMultiple(array $ids,$systemEntityID)
    {
        $model = $this->createModel();

        foreach($ids as $id){
            $model->deleteWithMedia($id,$systemEntityID);
        }
    }

    /**
     * Model has to use the SoftDeletes trait
     *
     * @see  \Illuminate\Database\Eloquent\SoftDeletes::forceDelete
     *
     * @param int $id
     * @param int $systemEntityID
     */
    public function destroyOne($id,$systemEntityID)
    {
        $model  = $this->createModel();
        $result = $model->withTrashed()->where($model->getKeyName(), $id)->first();
        if ( ! is_null($result)) {
            $result->forceDelete();
            if($model instanceof HasASystemEntity){
                $model->deleteWithMedia($id,$systemEntityID);
            }
        }
    }

    /**
     * Model has to use the SoftDeletes trait
     *
     * @see  \Illuminate\Database\Eloquent\SoftDeletes::forceDelete
     *
     * @param $id
     */
    public function restoreOne($id)
    {
        $model  = $this->createModel();
        $result = $model->withTrashed()->where($model->getKeyName(), $id)->first();
        if ( ! is_null($result)) {
            $result->restore();
        }
    }

    /**
     * Counts all occurrences in a table
     *
     * @return int
     */
    public function countAll()
    {
        $model = $this->createModel();

        /**
         * We substract one because a lot of tables have a dummy record of id 0 used for system purposes.
         */
        return $model->newQuery()
                     ->select(
                         \DB::raw(sprintf('count(%s)-1 as cnt', $model->getKeyName())))
                     ->value('cnt');
    }

}