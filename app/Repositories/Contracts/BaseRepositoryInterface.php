<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function find(int $id);
    public function findOrFail(int $id);
    public function all(array $columns = ['*']);
    public function paginate(int $perPage = 15);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function where(string $column, $value);
    public function whereIn(string $column, array $values);
    public function orderBy(string $column, string $direction = 'asc');
    public function with(array $relations);
}
