<?php

namespace App\Repositories;

use App\Models\Borrowing;
use App\Models\BookReturn;
use App\Models\ReturnLog;
use App\Repositories\Interfaces\ReturnRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReturnRepository implements ReturnRepositoryInterface
{
    public function getActiveBorrowings(string $search = ''): LengthAwarePaginator
    {
        return Borrowing::query()
            ->with(['member', 'details.book', 'returnLogs'])
            ->whereIn('status', [\App\Enums\BorrowingStatus::Dipinjam, \App\Enums\BorrowingStatus::Terlambat])
            ->whereHas('details', function ($q) {
                $q->whereColumn('qty_dikembalikan', '<', 'qty');
            })
            ->when(!empty($search), function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('kode_pinjam', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($m) => $m->where('nama', 'like', "%{$search}%"));
                });
            })
            ->orderBy('tanggal_jatuh_tempo')
            ->paginate(10, ['*'], 'active_page')
            ->withQueryString();
    }

    public function getReturnLogs(): LengthAwarePaginator
    {
        return ReturnLog::query()
            ->with(['borrowing.member', 'receiver'])
            ->latest()
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();
    }

    public function getCompletedReturns(): LengthAwarePaginator
    {
        return BookReturn::query()
            ->with(['borrowing.member', 'receiver'])
            ->latest()
            ->paginate(10, ['*'], 'completed_page')
            ->withQueryString();
    }
}
