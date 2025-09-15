<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\DataBase\Eloquent\Builder;
use Illuminate\DataBase\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface
{

    /**
     * Get Model Builder.
     *
     * @param array $cols
     * @param array $relations
     * @param array $condition
     * @param string $order
     * @param string $orderCol
     * @return Builder
     */
    public function builder(array $cols = ['*'], array $relations = [], array $condition = [], string $order = 'asc', string $orderCol = 'id'): Builder;


    /**
     * Get model data paginate.
     *
     * @param array $cols
     * @param array $relations
     * @param array $condition
     * @param string $order
     * @param string $orderCol
     * @param int $paginate
     * @return LengthAwarePaginator
     */
    public function paginate(array $cols = ['*'], array $relations = [], array $condition = [], string $order = 'asc', string $orderCol = 'id', int $paginate = 10): LengthAwarePaginator;
    


    /**
     * Get Model all data.
     *
     * @param array $cols
     * @param array $relations
     * @param array $condition
     * @param string $order
     * @param string $orderCol
     * @return Collection
     */
    public function all(array $cols = ['*'], array $relations = [], array $condition = [], string $order = 'asc', string $orderCol = 'id'): Collection;

    /**
     * Store model data.
     *
     * @param array $data
     * @return Model|null
     */
    public function store(array $data): ?Model;

    /**
     * update model data with id.
     *
     * @param array $data
     * @param int $id
     */
    public function update(int $id,array $data): ?Model;

    /**
     * find model by columns.
     *
     * @param array $cols
     */
    public function findByCols(array $cols): ?Model;

    /**
     * Desttroy model by id.
     *
     * @param int $id
     * @return bool
     */
    public function destroy(int $id): bool;


}