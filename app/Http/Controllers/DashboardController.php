<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Member;
use App\Models\Shelf;
use App\Models\User;
use App\Services\BorrowingService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected BorrowingService $borrowingService
    ) {}

    public function index(): View
    {
        $this->borrowingService->markOverdueBorrowings();

        $user = auth()->user();
        $isAdmin = $user->isAdminLibrary();

        $stats = [
            'members' => Member::count(),
            'books' => Book::count(),
            'categories' => Category::count(),
            'shelves' => Shelf::count(),
            'users' => User::count(),
            'borrowings' => Borrowing::count(),
            'borrowings_active' => Borrowing::whereIn('status', ['dipinjam', 'terlambat'])->count(),
            'fines' => Fine::where('status_bayar', 'belum_lunas')->count(),
            'fines_total' => Fine::where('status_bayar', 'belum_lunas')->sum('jumlah_denda'),
        ];

        if ($user->hasRole('Siswa') && $user->member) {
            $memberId = $user->member->id;
            $stats['my_borrowings'] = Borrowing::where('member_id', $memberId)->count();
            $stats['my_active'] = Borrowing::where('member_id', $memberId)
                ->whereIn('status', ['dipinjam', 'terlambat', 'diajukan'])->count();
            $stats['my_fines'] = Fine::where('member_id', $memberId)
                ->where('status_bayar', 'belum_lunas')->sum('jumlah_denda');
        }

        $recentBooks = Book::with(['category', 'shelf'])->latest()->limit(5)->get();
        $recentMembers = Member::with('user')->latest()->limit(5)->get();

        $recentBorrowingsQuery = Borrowing::with(['member', 'details'])
            ->latest()
            ->limit(5);

        if ($user->hasRole('Siswa') && $user->member) {
            $recentBorrowingsQuery->where('member_id', $user->member->id);
        }

        $recentBorrowings = $recentBorrowingsQuery->get();

        $lowStockBooks = $isAdmin
            ? Book::with('category')->where('stok_tersedia', '<=', 2)->orderBy('stok_tersedia')->limit(5)->get()
            : collect();

        $overdueBorrowings = $isAdmin
            ? Borrowing::with('member')->where('status', 'terlambat')->limit(5)->get()
            : collect();

        return view('dashboard', compact(
            'stats',
            'recentBooks',
            'recentMembers',
            'recentBorrowings',
            'lowStockBooks',
            'overdueBorrowings',
            'isAdmin'
        ));
    }
}
