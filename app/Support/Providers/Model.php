<?php namespace App\Support\Providers;

use App\Contracts\RawQueries;
use App\Models\Entity;
use Illuminate\Database\Eloquent\Builder;

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
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildOne($id)
    {
        $model = $this->createModel();

        return $model->newQuery()->where(
            sprintf('%s.%s',
                $model->getTable(),
                $model->getKeyName()
            ), $id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function noScopes()
    {
        return $this->createModel()->newQueryWithoutScopes();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function build()
    {
        return $this->createModel()->newQuery();
    }

    /**
     * @param array $columns
     * @param array $scopes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildWithScopes(array $columns, array $scopes): Builder
    {
        return $this->select($columns)->scopes($scopes);
    }

    /**
     * @param array $columns
     * @param array $scopes
     * @param array $wheres
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildOneWithScopes(array $columns, array $scopes, array $wheres): Builder
    {
        $q = $this->select($columns)->scopes($scopes);
        foreach ($wheres as $where) {
            list($column, $value) = $where;
            if (!is_array($value)) {
                $q->where($column, $value);
            } else {
                $q->whereIn($column, $value);
            }
        }
        return $q;
    }

    /**
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function select($columns = ['*'])
    {
        return $this->createModel()->newQuery()->select($columns);
    }

    /**
     * Counts all occurrences in a table
     *
     * @return int
     */
    public function countAll()
    {
        $model = $this->createModel();

        return intval($model->newQuery()
            ->select(
                \DB::raw(sprintf('count(%s) as cnt', $model->getKeyName())))
            ->value('cnt'));
    }

    /**
     * Filter the data array only keeping items matching the model's
     * fillable property
     *
     * @param array $data
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return array
     */
    public function filterFillables($data, $model = null)
    {
        if (is_null($model)) {
            $model = $this->createModel();
        }
        $fillables = array_flip($model->getFillable());

        return array_filter($data, function ($key) use ($fillables) {
            return isset($fillables[$key]);
        }, ARRAY_FILTER_USE_KEY);
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

    public function getKeyName()
    {
        return $this->createModel()->getKeyName();
    }

    public function getQualifiedKeyName()
    {
        return $this->createModel()->getQualifiedKeyName();
    }

    /**
     * @param $userID
     * @param \App\Filters\Filters $filter
     */
    public function setStoredFilter($userID, $filter)
    {
        if ($filter->hasFilters()) {
            \Cache::put($this->getStoredFilterKey($userID), $filter, 1800);
        } else {
            \Cache::forget($this->getStoredFilterKey($userID));
        }
    }

    /**
     * @param $userID
     * @param $entityID
     * @return \App\Filters\Filters|\null
     */
    public function getStoredFilter($userID, $entityID)
    {
        $filterID = $this->getStoredFilterKey($userID);
        if (\Cache::has($filterID)) {
            $filter = \Cache::get($filterID);
            if (Entity::getModelClassNamespace($entityID, false) === $filter->getFiltersFor()) {
                return $filter;
            }
        }
        return null;
    }

    /**
     * @param int $userID
     * @return string
     */
    private function getStoredFilterKey($userID)
    {
        return sprintf('backend_filter_%s', $userID);
    }

    /**
     * @param int $entityTypeID
     * @return mixed
     */
    public function getAllUserPermissions($entityTypeID)
    {
        return (app(RawQueries::class))->getAllUserPermissions($entityTypeID);
    }

}