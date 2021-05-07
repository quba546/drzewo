<?php

declare(strict_types=1);


namespace App\Repository;

use App\Models\Category;
use App\Repository\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;


class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private Category $category)
    {
    }

    public function getAllStructured(int $id, string $direction): Collection
    {
        return $this->category->where('parent_id', '=', $id)
            ->orderBy('title', $direction)
            ->get();
    }

    public function getAll(array $columns = ['*']): Collection
    {
        return $this->category->get($columns);
    }

    public function checkIfDoesntExist(int $id): bool
    {
        return $this->category->where('id', '=', $id)
            ->doesntExist();
    }

    public function insert(string $title, int $parentId): void
    {
        $this->category->create([
            'title' => $title,
            'parent_id' => $parentId
        ]);
    }

    public function getOne(int $parentId): Category
    {
        return $this->category->where('id', '=', $parentId)
            ->firstOrFail('parent_id');
    }

    public function edit(int $id, string $column, int|string $newData): void
    {
        $this->category->where('id', '=', $id)
            ->update([$column => $newData]);
    }

    public function delete(int $id): void
    {
        $this->category->where('id', '=', $id)
            ->delete();
    }

    public function getAllChildren(int $id, array $columns = ['*']): Collection
    {
        return $this->category->where('parent_id', '=', $id)
            ->get($columns);
    }
}
