<?php

namespace App\Repositories\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ReturnRepositoryInterface
{
    public function getActiveBorrowings(string $search = ''): LengthAwarePaginator;
    public function getReturnLogs(): LengthAwarePaginator;
    public function getCompletedReturns(): LengthAwarePaginator;
}
