<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function __construct(protected BookRepositoryInterface $bookRepository)
    {
    }

    public function getAllBooks(string $search = null): LengthAwarePaginator
    {
        return $this->bookRepository->getAll($search);
    }

    public function createBook(array $data): Book
    {
        if (isset($data['cover']) && $data['cover'] instanceof UploadedFile) {
            $data['cover'] = $data['cover']->store('covers', 'public');
        }

        if (! isset($data['stok_tersedia'])) {
            $data['stok_tersedia'] = $data['jumlah_buku'];
        }

        return $this->bookRepository->create($data);
    }

    public function updateBook(Book $book, array $data): bool
    {
        if (isset($data['cover']) && $data['cover'] instanceof UploadedFile) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            $data['cover'] = $data['cover']->store('covers', 'public');
        }

        return $this->bookRepository->update($book, $data);
    }

    public function deleteBook(Book $book): bool
    {
        return $this->bookRepository->delete($book);
    }
}
