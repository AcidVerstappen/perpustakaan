<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class BookRepository implements BookRepositoryInterface
{
    public function getAll(string $search = null): LengthAwarePaginator
    {
        return Book::query()
            ->with(['category', 'shelf'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('judul', 'like', "%{$search}%")
                        ->orWhere('kode_buku', 'like', "%{$search}%")
                        ->orWhere('penulis', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();
    }

    public function create(array $data): Book
    {
        return Book::create($data);
    }

    public function update(Book $book, array $data): bool
    {
        return $book->update($data);
    }

    public function delete(Book $book): bool
    {
        if ($book->cover) {
            Storage::disk('public')->delete($book->cover);
        }

        return $book->delete();
    }
}
