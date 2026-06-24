<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Member;
use App\Models\Shelf;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct(protected BorrowingService $borrowingService)
    {
    }

    public function getStats(User $user, bool $isAdmin): array
    {
        Cache::remember('borrowings:mark-overdue:last-run', now()->addMinute(), function () {
            $this->borrowingService->markOverdueBorrowings();
            return true;
        });

        $stats = [
            'members' => Member::count(),
            'books' => Book::count(),
            'categories' => Category::count(),
            'shelves' => Shelf::count(),
            'users' => User::count(),
            'borrowings' => Borrowing::count(),
            'borrowings_active' => Borrowing::whereIn('status', [\App\Enums\BorrowingStatus::Dipinjam, \App\Enums\BorrowingStatus::Terlambat])->count(),
            'fines' => Fine::where('status_bayar', \App\Enums\FineStatus::BelumLunas)->count(),
            'fines_total' => Fine::where('status_bayar', \App\Enums\FineStatus::BelumLunas)->sum('jumlah_denda'),
        ];

        if ($user->hasRole('Siswa') && $user->member) {
            $memberId = $user->member->id;
            $stats['my_borrowings'] = Borrowing::where('member_id', $memberId)->count();
            $stats['my_active'] = Borrowing::where('member_id', $memberId)
                ->whereIn('status', [\App\Enums\BorrowingStatus::Dipinjam, \App\Enums\BorrowingStatus::Terlambat, \App\Enums\BorrowingStatus::Diajukan])->count();
            $stats['my_fines'] = Fine::where('member_id', $memberId)
                ->where('status_bayar', \App\Enums\FineStatus::BelumLunas)->sum('jumlah_denda');
        }

        return $stats;
    }

    public function getRecentBooks()
    {
        return Book::with(['category', 'shelf'])->latest()->limit(5)->get();
    }

    public function getRecentMembers()
    {
        return Member::with('user')->latest()->limit(5)->get();
    }

    public function getRecentBorrowings(User $user)
    {
        $query = Borrowing::with(['member', 'details'])->latest()->limit(5);

        if ($user->hasRole('Siswa') && $user->member) {
            $query->where('member_id', $user->member->id);
        }

        return $query->get();
    }

    public function getLowStockBooks(bool $isAdmin)
    {
        return $isAdmin
            ? Book::with('category')->where('stok_tersedia', '<=', 2)->orderBy('stok_tersedia')->limit(5)->get()
            : collect();
    }

    public function getOverdueBorrowings(bool $isAdmin)
    {
        return $isAdmin
            ? Borrowing::with('member')->where('status', \App\Enums\BorrowingStatus::Terlambat)->limit(5)->get()
            : collect();
    }
}
