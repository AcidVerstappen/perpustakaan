<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\BookReturnController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BookQrController;
use App\Http\Controllers\BookBookingController;
use App\Http\Controllers\ShelfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
    Route::get('fines', [FineController::class, 'index'])->name('fines.index');

    Route::middleware(['role:Super Admin|Petugas'])->group(function () {
        Route::get('borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create');
        Route::post('borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
        Route::delete('borrowings/{borrowing}', [BorrowingController::class, 'destroy'])->name('borrowings.destroy');
        Route::post('borrowings/{borrowing}/approve', [BorrowingController::class, 'approve'])
            ->name('borrowings.approve');
        Route::post('borrowings/{borrowing}/reject', [BorrowingController::class, 'reject'])
            ->name('borrowings.reject');

        Route::get('returns', [BookReturnController::class, 'index'])->name('returns.index');
        Route::get('returns/create/{borrowing}', [BookReturnController::class, 'create'])->name('returns.create');
        Route::post('returns/{borrowing}', [BookReturnController::class, 'store'])->name('returns.store');
    });

    // Petugas: read-only access to members
    Route::middleware(['role:Super Admin|Petugas'])->group(function () {
        Route::get('members', [MemberController::class, 'index'])->name('members.index');
    });

    // Super Admin only — master data & system management
    Route::middleware(['role:Super Admin'])->group(function () {
        Route::get('books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('books', [BookController::class, 'store'])->name('books.store');
        Route::get('books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::patch('books/{book}', [BookController::class, 'update']);
        Route::delete('books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

        Route::post('fines/{fine}/pay', [FineController::class, 'pay'])->name('fines.pay');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('shelves', ShelfController::class)->except(['show']);
        Route::resource('members', MemberController::class)->except(['show', 'index']);

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/books/pdf', [ReportController::class, 'books'])->name('reports.books');
        Route::get('reports/members/pdf', [ReportController::class, 'members'])->name('reports.members');
        Route::get('reports/borrowings/pdf', [ReportController::class, 'borrowings'])->name('reports.borrowings');
        Route::get('reports/fines/pdf', [ReportController::class, 'fines'])->name('reports.fines');

        Route::get('books/{book}/qr', [BookQrController::class, 'show'])->name('books.qr');

        Route::get('qr-scan', [\App\Http\Controllers\QrScanController::class, 'index'])->name('qr-scan.index');
        Route::post('qr-scan', [\App\Http\Controllers\QrScanController::class, 'handle'])->name('qr-scan.handle');
    });

    Route::middleware(['role:Siswa'])->group(function () {
        Route::get('booking/cart', [BookBookingController::class, 'cart'])->name('booking.cart');
        Route::post('booking/cart/add/{book}', [BookBookingController::class, 'add'])->name('booking.cart.add');
        Route::delete('booking/cart/remove/{book}', [BookBookingController::class, 'remove'])->name('booking.cart.remove');
        Route::post('booking/checkout', [BookBookingController::class, 'checkout'])->name('booking.checkout');
    });

    Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');
    Route::get('borrowings/{borrowing}', [BorrowingController::class, 'show'])->name('borrowings.show');
    Route::get('borrowings/{borrowing}/qr', [BorrowingController::class, 'qr'])->name('borrowings.qr');
    Route::get('fines/{fine}', [FineController::class, 'show'])->name('fines.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
