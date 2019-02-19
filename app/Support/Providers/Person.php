<?php namespace App\Support\Providers;

use App\Contracts\Models\Person as PersonInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method \App\Models\Person createModel(array $attributes = [])
 * @method \App\Models\Person getOne($id, $columns = ['*'])
 */
class Person extends Model implements PersonInterface
{
    protected $model = \App\Models\Person::class;

    public function buildOneBySlug($slug, $columns = ['*']): Builder
    {
        return $this->createModel()->newQuery()
            ->select($columns)->where('person_slug', $slug);

    }

    /**
     * @param string $search
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function search($search, $limit): Builder
    {
        return $this->createModel()->newQuery()
            ->select(['full_name as text', 'person_slug as id'])
            ->where('full_name', 'like', sprintf('%%%s%%', $search))
            ->limit($limit);

    }

}