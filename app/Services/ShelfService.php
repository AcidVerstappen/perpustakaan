<?php

namespace App\Services;

use App\Exceptions\ShelfInUseException;
use App\Models\Shelf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShelfService
{
    public function getAllShelves(string $search = null): LengthAwarePaginator
    {
        return Shelf::query()
            ->withCount('books')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('kode_rak', 'like', "%{$search}%")
                        ->orWhere('nama_rak', 'like', "%{$search}%")
                        ->orWhere('lokasi', 'like', "%{$search}%");
                });
            })
            ->orderBy('kode_rak')
            ->paginate(10)
            ->withQueryString();
    }

    public function createShelf(array $data): Shelf
    {
        return Shelf::create($data);
    }

    public function updateShelf(Shelf $shelf, array $data): bool
    {
        return $shelf->update($data);
    }

    public function deleteShelf(Shelf $shelf): bool
    {
        if ($shelf->books()->exists()) {
            throw new ShelfInUseException();
        }

        return $shelf->delete();
    }
}
