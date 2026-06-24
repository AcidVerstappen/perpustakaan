<?php

namespace App\Repositories\Interfaces;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BookRepositoryInterface
{
    public function getAll(string $search = null): LengthAwarePaginator;
    public function create(array $data): Book;
    public function update(Book $book, array $data): bool;
    public function delete(Book $book): bool;
}
