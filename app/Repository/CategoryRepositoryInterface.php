<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getAllStructured(int $id, string $direction): Collection;

    public function getAll(array $columns = ['*']): Collection;

    public function checkIfDoesntExist(int $id): bool;

    public function insert(string $title, int $parentId): void;

    public function getOne(int $parentId): Category;

    public function edit(int $id, string $column, int|string $newData): void;

    public function delete(int $id): void;

    public function getAllChildren(int $id, array $columns = ['*']): Collection;
}
