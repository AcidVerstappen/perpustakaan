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
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected \App\Services\DashboardService $dashboardService
    ) {}

    public function index(): View
    {
        $user = auth()->user()->loadMissing('member');
        $isAdmin = $user->isAdminLibrary();

        $stats = $this->dashboardService->getStats($user, $isAdmin);
        $recentBooks = $this->dashboardService->getRecentBooks();
        $recentMembers = $this->dashboardService->getRecentMembers();
        $recentBorrowings = $this->dashboardService->getRecentBorrowings($user);
        $lowStockBooks = $this->dashboardService->getLowStockBooks($isAdmin);
        $overdueBorrowings = $this->dashboardService->getOverdueBorrowings($isAdmin);

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
