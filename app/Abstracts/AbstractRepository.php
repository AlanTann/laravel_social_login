<?php

namespace App\Abstracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Interfaces\RepositoryInterface;

// Pixlr Market Aerospike
// use App\Acme\AerospikeCacheKeyGenerator;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * Eloquent model to present the database table.
     *
     * @var Eloquent\Model
     */
    protected $model;

    /**
     * Eloquent Builder.
     *
     * @var Eloquent\Builder
     */
    protected $query_builder;

    /**
     * @var string
     */
    protected $cache_key;

    /**
     * @var bool
     */
    protected $enable_cache = false;

    /**
     * Duration in minutes.
     *
     * @var int
     */
    protected $cache_duration = 10080;

    /**
     * @var array
     */
    protected $where_column = [];

    /**
     * @var array
     */
    protected $select_column = [];

    /**
     * Force every repository class has to init $model when want to use.
     */
    public function __construct()
    {
        $this->initializeModel();
    }

    /**
     * @return Eloquent\Model
     */
    public function getEloquentModel()
    {
        return $this->model;
    }

    /**
     * @return self
     */
    public function find()
    {
        $this->unsetBuilder();
        if (App::isLocal()){
            $this->query_builder = $this->model->on('thelink')->newQuery();
        } else{
            $this->query_builder = $this->model->query();
        }

        return $this;
    }

    /**
     * @param mixed $id
     */
    public function byId($id)
    {
        $this->query_builder->where($this->model->getKeyName(), '=', $id);

        return $this;
    }

    /**
     * @return Eloquent\Collection
     */
    public function get()
    {
        return $this->query_builder->get();
    }

    /**
     * @return Model
     */
    public function first()
    {
        //go through the Aerospike flow if enable_cache is true
        if ($this->enable_cache) {
            $this->generateCacheKey();
            $model_cache = $this->getCache();

            if (isset($model_cache)) {
                $model = unserialize(base64_decode($model_cache));

                return $model;
            } else {
                $model = base64_encode(serialize($this->query_builder->first()));
                $this->putCache($model);
            }
        }

        return $this->query_builder->first();
    }

    /**
     * @return Eloquent\Collection
     */
    public function all()
    {
        return $this->query_builder->all();
    }

    /**
     * @param string $column
     * @param string $direction
     *
     * @return self
     */
    public function orderBy(string $column, string $direction = 'asc')
    {
        $this->query_builder->orderBy($column, $direction);

        return $this;
    }

    /**
     * @param string $column_name
     *
     * @return self
     */
    public function groupBy(string $column_name)
    {
        $this->query_builder->groupBy($column_name);

        return $this;
    }

    /**
     * @param int $quantity
     *
     * @return self
     */
    public function limit(int $quantity = 10)
    {
        $this->query_builder->limit($quantity);

        return $this;
    }

    /**
     * @param AbstractModel $model
     */
    public function updateOrInsert(AbstractModel $model)
    {
        $model->setConnection('master');
        return $model->save();
    }

    /**
     * @param AbstractModel $model
     *
     * @return bool
     */
    public function update(AbstractModel $model)
    {
        $model->setConnection('master');
        return $model->update();
    }

    /**
     * @param mixed $relations
     *
     * @return self
     */
    public function with($relations)
    {
        $this->query_builder->with($relations);

        return $this;
    }

    /**
     * @param string $column
     */
    public function max(string $column)
    {
        return $this->query_builder->max($column);
    }

    public function toSql()
    {
        return $this->query_builder->toSql();
    }

    /**
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     */
    public function where(string $column, $value, $operator = '=')
    {
        $this->query_builder->where($column, $operator, $value);

        return $this;
    }

    /**
     * @param string
     * @param array
     */
    public function orderByRaw(string $orderby_query, array $bindings = [])
    {
        $this->query_builder->orderByRaw($orderby_query, $bindings);

        return $this;
    }

    /**
     * Set the columns to be selected.
     *
     * @param array|mixed $columns
     *
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->query_builder->select($columns);

        return $this;
    }

    /**
     * @param string
     * @param array
     */
    public function selectRaw(string $select_query, array $bindings = [])
    {
        $this->query_builder->selectRaw($select_query, $bindings);

        return $this;
    }

    protected function unsetBuilder()
    {
        unset($this->query_builder);
    }

    /**
     * @return self
     */
    public function exists()
    {
        return $this->query_builder->exists();
    }

    /**
     * @param string $join_table_name
     * @param string $join_table_column
     * @param string $table_column
     *
     * @return self
     */
    public function join(string $join_table_name, string $join_table_column, string $table_column)
    {
        $this->query_builder->join($join_table_name, "$join_table_name.$join_table_column", '=', $this->appendTableNameAsPrefix($table_column));

        return $this;
    }

    /**
     * @param string $columns
     *
     * @return int
     */
    public function count(string $columns = '*')
    {
        return $this->query_builder->count($columns);
    }

    /**
     * @param int $offset
     *
     * @return self
     */
    public function offset(int $offset = 0)
    {
        $this->query_builder->offset($offset);

        return $this;
    }

    /**
     * @param string $column
     *
     * @return string
     */
    public function appendTableNameAsPrefix(string $column)
    {
        return $this->model->getTable().'.'.$column;
    }

    /**
     * @return string|null
     */
    public function getCacheKey()
    {
        return $this->cache_key;
    }

    /**
     * @param string $cache_key
     */
    public function setCacheKey(string $cache_key)
    {
        $this->cache_key = $cache_key;
    }

    /**
     * @return string
     */
    //Pixlr Market Aerospike
    // protected function generateCacheKey()
    // {
    //     $cache_key = AerospikeCacheKeyGenerator::generate($this->model->getTable(), $this->where_column, $this->select_column);
    //     $this->setCacheKey($cache_key);
    // }

    /**
     * @param $value
     *
     * @return bool
     */
    public function putCache($value)
    {
        return Cache::put($this->cache_key, $value, $this->cache_duration);
    }

    /**
     * @return array|null
     */
    public function getCache()
    {
        return Cache::get($this->cache_key);
    }

    /**
     * Get the value of enable_cache.
     *
     * @return bool
     */
    public function getEnableCache()
    {
        return $this->enable_cache;
    }

    /**
     * Set the value of enable_cache.
     *
     * @param bool $enable_cache
     */
    public function setEnableCache(bool $enable_cache)
    {
        $this->enable_cache = $enable_cache;
    }

    /**
     * Get duration in minutes.
     *
     * @return int
     */
    public function getCacheDuration()
    {
        return $this->cache_duration;
    }

    /**
     * Set duration in minutes.
     *
     * @param int $cache_duration Duration in minutes
     */
    public function setCacheDuration(int $cache_duration)
    {
        $this->cache_duration = $cache_duration;
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return LengthAwarePaginator
     */
    public function paginate(int $limit, int $offset = 0)
    {
        $paginator = new LengthAwarePaginator([], $this->count(), $limit, Paginator::resolveCurrentPage());

        $paginator->setCollection($this->offset($offset)->limit($limit)->get());

        return $paginator;
    }
}
