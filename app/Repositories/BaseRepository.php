<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findOrFail(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function all(array $columns = ['*'])
    {
        return $this->model->select($columns)->get();
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id)
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }

    public function where(string $column, $value)
    {
        return $this->model->where($column, $value);
    }

    public function whereIn(string $column, array $values)
    {
        return $this->model->whereIn($column, $values);
    }

    public function orderBy(string $column, string $direction = 'asc')
    {
        return $this->model->orderBy($column, $direction);
    }

    public function with(array $relations)
    {
        return $this->model->with($relations);
    }
}
