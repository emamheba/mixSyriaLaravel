<?php

namespace App\Repositories\Main;

use App\Contracts\EloquentRepositoryInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EloquentMainRepository implements EloquentRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }


    public function builder(array $cols = ['*'], array $relations = [], array $condition = [], string $order = 'asc', string $orderCol = 'id'): Builder
    {
        return $this->model::with($relations)->where($condition)->select($cols)->orderBy($orderCol, $order);
    }


    public function paginate(array $cols = ['*'], array $relations = [], array $condition = [], string $order = 'asc', string $orderCol = 'id', int|null $paginate = 10): LengthAwarePaginator
    {
        return $this->builder($cols, $relations, $condition, $order, $orderCol)->paginate($paginate ?? 10);
    }

    public function cursorPaginate(array $cols = ['*'], array $relations = [], array $condition = [], string $order = 'asc', string $orderCol = 'id', int|null $paginate = 10): CursorPaginator
    {
        return $this->builder($cols, $relations, $condition, $order, $orderCol)->cursorPaginate($paginate ?? 10);
    }


    public function all(array $cols = ['*'], array $relations = [], array $condition = [], string $order = 'asc', string $orderCol = 'id'): Collection
    {
        return $this->builder($cols, $relations, $condition, $order, $orderCol)->get();
    }

    public function store(array $data): Model
    {
        return $this->model::create($data);
    }

    public function update(int $id, array $data): Model
    {
        $team = $this->findByCols(['id' => $id]);
        $team->update($data);
        return $team;
    }
    public function updateWithReturn(int $id, array $data): Model
    {
        $team = $this->findByCols(['id' => $id]);
        return tap($team)->udpate($data);
    }

    // public function findByCols(array $cols, array $with = [], array $select = ['*']): Model
    // {
    //     return $this->model::with($with)->select($select)->where($cols)->first();
    // }

    public function findByCols(array $cols, array $with = [], array $select = ['*']): ?Model
{
    return $this->model::with($with)->select($select)->where($cols)->first();
}


    
    public function find(array $cols, array $conditions): Model
    {
        return $this->model::select($cols)->where($conditions)->first();
    }

    public function destroy(int $id): bool
    {
        return $this->model::find($id)->delete();
    }

    public function forceDelete(int $id): bool
    {
        return $this->model::withTrashed()->find($id)->forceDelete();
    }

    public function trashedRestore(int $id): bool
    {
        return $this->model::withTrashed()->find($id)->restore();
    }
}