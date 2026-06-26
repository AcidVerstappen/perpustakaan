<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    public function generateBooksReport()
    {
        $books = Book::with(['category', 'shelf'])
            ->orderBy('judul')
            ->limit(1000)
            ->get();

        return Pdf::loadView('reports.pdf.books', [
            'books' => $books,
            'generatedAt' => now(),
            'limited' => Book::count() > 1000,
        ])->setPaper('a4', 'landscape')->download('laporan-buku-' . now()->format('Y-m-d') . '.pdf');
    }

    public function generateBorrowingsReport(?string $dari, ?string $sampai, ?string $status)
    {
        $borrowings = Borrowing::with(['member', 'details.book', 'processor'])
            ->when($dari, fn ($q) => $q->whereDate('tanggal_pinjam', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('tanggal_pinjam', '<=', $sampai))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('tanggal_pinjam')
            ->limit(1000)
            ->get();

        return Pdf::loadView('reports.pdf.borrowings', [
            'borrowings' => $borrowings,
            'filters' => compact('dari', 'sampai', 'status'),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape')->download('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
    }

    public function generateFinesReport(?string $statusBayar)
    {
        $fines = Fine::with(['member', 'borrowing'])
            ->when($statusBayar, fn ($q) => $q->where('status_bayar', $statusBayar))
            ->orderByDesc('created_at')
            ->limit(1000)
            ->get();

        return Pdf::loadView('reports.pdf.fines', [
            'fines' => $fines,
            'filters' => ['status_bayar' => $statusBayar],
            'generatedAt' => now(),
        ])->download('laporan-denda-' . now()->format('Y-m-d') . '.pdf');
    }

    public function generateMembersReport()
    {
        $members = Member::with('user')
            ->orderBy('nama')
            ->limit(1000)
            ->get();

        return Pdf::loadView('reports.pdf.members', [
            'members' => $members,
            'generatedAt' => now(),
            'limited' => Member::count() > 1000,
        ])->setPaper('a4', 'landscape')->download('laporan-anggota-' . now()->format('Y-m-d') . '.pdf');
    }
}
