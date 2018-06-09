<?php namespace App\Support\Providers;

use App\Contracts\HasAnEntity;

abstract class Model
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
        if (!empty($model)) {
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
    public function getModelName()
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
            ->where($model->getKeyName(), '>', 1)
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
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function build()
    {
        return $this->createModel();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function noScopes()
    {
        return $this->createModel()->newQueryWithoutScopes();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select($select)
    {
        return $this->createModel()->select($select);
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
     * For models using the HasAnEntity trait
     *
     * @see \App\Traits\Models\HasAnEntity
     * @param int $id
     * @param int $entityID
     *
     * @return mixed
     */
    public function deleteWithMedia($id, $entityID)
    {
        $model = $this->createModel();

        return $model->deleteWithMedia($id, $entityID);
    }

    /**
     * @param array $ids
     * @param int $entityID
     */
    public function deleteMultiple(array $ids, $entityID)
    {
        $model = $this->createModel();

        foreach ($ids as $id) {
            $model->deleteWithMedia($id, $entityID);
        }
    }

    /**
     * Model has to use the SoftDeletes trait
     *
     * @see  \Illuminate\Database\Eloquent\SoftDeletes::forceDelete
     *
     * @param int $id
     * @param int $entityID
     */
    public function destroyOne($id, $entityID)
    {
        $model = $this->createModel();
        $result = $model->withTrashed()->where($model->getKeyName(), $id)->first();
        if (!is_null($result)) {
            $result->forceDelete();
            if ($model instanceof HasAnEntity) {
                $model->deleteWithMedia($id, $entityID);
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
        $model = $this->createModel();
        $result = $model->withTrashed()->where($model->getKeyName(), $id)->first();
        if (!is_null($result)) {
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

    /**
     * Filter the data array only keeping items matching the model's
     * fillable property
     *
     * @param array $data
     * @return array
     */
    public function filterFillables($data)
    {
        $fillables = array_flip($this->createModel()->getFillable());

        return array_filter($data, function ($key) use ($fillables) {
            return isset($fillables[$key]);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param string $table
     * @return int|null
     */
    public static function getLastID($table)
    {
        $result = \DB::select(
            sprintf('SELECT AUTO_INCREMENT AS c FROM information_schema.tables
                WHERE table_name = "%s" AND table_schema = "%s"',
                (new $table)->getTable(),
                config('database.connections.mysql.database')
            )
        );
        return !empty($result) ? $result[0]->c : null;

    }

    /**
     * @param string $model
     * @return int|null
     */
    public static function getRowCount($model)
    {
        /** @var \Illuminate\Database\Eloquent\Model $m */
        $m = new $model;
        $result = \DB::select(
            sprintf('SELECT count(%s) AS c FROM %s', $m->getKeyName(), $m->getTable()));
        return !empty($result) ? $result[0]->c : null;

    }

}