<?php

namespace App\Services;

use App\Exceptions\CategoryInUseException;
use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CategoryService
{
    public function getAllCategories(string $search = null): LengthAwarePaginator
    {
        return Category::query()
            ->withCount('books')
            ->when($search, fn ($q) => $q->where('nama_kategori', 'like', "%{$search}%"))
            ->orderBy('nama_kategori')
            ->paginate(10)
            ->withQueryString();
    }

    public function createCategory(array $data): Category
    {
        return Category::create($data);
    }

    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function deleteCategory(Category $category): bool
    {
        if ($category->books()->exists()) {
            throw new CategoryInUseException();
        }

        return $category->delete();
    }
}
