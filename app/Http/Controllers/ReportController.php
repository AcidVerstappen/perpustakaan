<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(protected \App\Services\ReportService $reportService)
    {
    }

    public function index(): View
    {
        return view('reports.index');
    }

    public function books(Request $request): Response
    {
        return $this->reportService->generateBooksReport();
    }

    public function borrowings(Request $request): Response
    {
        $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'status' => ['nullable', 'in:diajukan,dipinjam,terlambat,selesai,ditolak'],
        ]);

        return $this->reportService->generateBorrowingsReport(
            $request->dari,
            $request->sampai,
            $request->status
        );
    }

    public function fines(Request $request): Response
    {
        $request->validate([
            'status_bayar' => ['nullable', 'in:belum_lunas,lunas'],
        ]);

        return $this->reportService->generateFinesReport($request->status_bayar);
    }

    public function members(): Response
    {
        return $this->reportService->generateMembersReport();
    }
}
