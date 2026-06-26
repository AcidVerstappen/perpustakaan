<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnBookRequest;
use App\Models\Borrowing;
use App\Services\BorrowingService;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class BookReturnController extends Controller
{
    public function __construct(
        protected ReturnService $returnService,
        protected BorrowingService $borrowingService
    ) {}

    public function index(): View
    {
        $request = request();

        Cache::remember('borrowings:mark-overdue:last-run', now()->addMinute(), function () {
            $this->borrowingService->markOverdueBorrowings();
            return true;
        });

        $search = $request->string('search')->trim();

        $activeBorrowings = $this->returnService->getActiveBorrowings($search);
        $returnLogs = $this->returnService->getReturnLogs();
        $completedReturns = $this->returnService->getCompletedReturns();

        return view('returns.index', compact('activeBorrowings', 'returnLogs', 'completedReturns', 'search'));
    }

    public function create(Borrowing $borrowing): View|RedirectResponse
    {
        if (! $borrowing->canBeReturned()) {
            return redirect()
                ->route('returns.index')
                ->with('error', 'Peminjaman ini tidak dapat dikembalikan atau sudah lunas.');
        }

        $borrowing->load(['member', 'details.book', 'returnLogs']);
        $isPetugas = auth()->user()->isPetugas();

        return view('returns.create', compact('borrowing', 'isPetugas'));
    }

    public function store(ReturnBookRequest $request, Borrowing $borrowing): RedirectResponse
    {
        $borrowing = Borrowing::with(['details.book', 'bookReturn'])->findOrFail($borrowing->id);

        $request->validate([
            'tanggal_kembali' => ['nullable', 'date', 'after_or_equal:'.$borrowing->tanggal_pinjam->format('Y-m-d')],
        ]);

        $returnDate = $request->filled('tanggal_kembali')
            ? \Carbon\Carbon::parse($request->tanggal_kembali)
            : now();

        $returnedQtyByDetail = collect($request->input('items', []))
            ->mapWithKeys(fn ($item, $detailId) => [(int) $detailId => (int) $item['qty']])
            ->all();

        $result = $this->returnService->process(
            $borrowing,
            $request->user(),
            $returnDate,
            $returnedQtyByDetail,
            $request->input('kondisi_buku'),
            $request->input('catatan_kondisi')
        );

        $stockSummary = collect($result->restoredBooks)
            ->map(fn ($r) => "{$r['judul']} (+{$r['qty']}, sisa pinjam: {$r['sisa_setelah']})")
            ->implode('; ');

        if ($result->isFullyReturned) {
            $message = "Semua buku sudah dikembalikan. Stok ditambah: {$stockSummary}.";
            if ($result->bookReturn && $result->bookReturn->total_denda > 0) {
                $message .= ' Total denda Rp'.number_format($result->bookReturn->total_denda, 0, ',', '.').'.';
            }
        } else {
            $message = "Pengembalian sebagian berhasil. Stok ditambah: {$stockSummary}. ";
            $message .= "Sisa belum dikembalikan: {$result->totalSisa} eksemplar.";
            if ($result->dendaTambahan > 0) {
                $message .= ' Denda tambahan Rp'.number_format($result->dendaTambahan, 0, ',', '.').'.';
            }
        }

        return redirect()
            ->route('returns.index')
            ->with('success', $message);
    }
}
