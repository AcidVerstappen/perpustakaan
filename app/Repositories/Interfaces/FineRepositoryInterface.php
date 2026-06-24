<?php

namespace App\Repositories\Interfaces;

use App\Models\Fine;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FineRepositoryInterface
{
    public function getAll(string $search = '', string $status = '', ?User $user = null): LengthAwarePaginator;
    public function getTotalBelumLunas(?User $user = null): int;
    public function findById(int $id): ?Fine;
    public function updateStatus(Fine $fine, string $status, ?string $tanggalBayar = null): bool;
}
