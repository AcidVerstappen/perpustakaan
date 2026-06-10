<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Fine;
use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function books(Request $request): Response
    {
        $books = Book::with(['category', 'shelf'])
            ->orderBy('judul')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.books', [
            'books' => $books,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-buku-'.now()->format('Y-m-d').'.pdf');
    }

    public function borrowings(Request $request): Response
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'status' => ['nullable', 'in:diajukan,dipinjam,terlambat,selesai,ditolak'],
        ]);

        $borrowings = Borrowing::with(['member', 'details.book', 'processor'])
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('tanggal_pinjam', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tanggal_pinjam', '<=', $request->sampai))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('tanggal_pinjam')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.borrowings', [
            'borrowings' => $borrowings,
            'filters' => $request->only(['dari', 'sampai', 'status']),
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-peminjaman-'.now()->format('Y-m-d').'.pdf');
    }

    public function fines(Request $request): Response
    {
        $request->validate([
            'status_bayar' => ['nullable', 'in:belum_lunas,lunas'],
        ]);

        $fines = Fine::with(['member', 'borrowing'])
            ->when($request->filled('status_bayar'), fn ($q) => $q->where('status_bayar', $request->status_bayar))
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.fines', [
            'fines' => $fines,
            'filters' => $request->only(['status_bayar']),
            'generatedAt' => now(),
        ]);

        return $pdf->download('laporan-denda-'.now()->format('Y-m-d').'.pdf');
    }

    public function members(): Response
    {
        $members = Member::with('user')->orderBy('nama')->get();

        $pdf = Pdf::loadView('reports.pdf.members', [
            'members' => $members,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-anggota-'.now()->format('Y-m-d').'.pdf');
    }
}
