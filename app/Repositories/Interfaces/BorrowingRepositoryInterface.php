<?php

namespace App\Repositories\Interfaces;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BorrowingRepositoryInterface
{
    public function getAll(string $search = '', string $status = '', ?User $user = null): LengthAwarePaginator;
    public function findById(int $id): ?Borrowing;
}
