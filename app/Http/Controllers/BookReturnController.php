<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnBookRequest;
use App\Models\Borrowing;
use App\Services\BorrowingService;
use App\Services\ReturnService;
use Illuminate\Http\RedirectResponse;
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
        $this->borrowingService->markOverdueBorrowings();

        $search = $request->string('search')->trim();

        $activeBorrowings = Borrowing::query()
            ->with(['member', 'details.book', 'returnLogs'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->whereHas('details', function ($q) {
                $q->whereColumn('qty_dikembalikan', '<', 'qty');
            })
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('kode_pinjam', 'like', "%{$search}%")
                        ->orWhereHas('member', fn ($m) => $m->where('nama', 'like', "%{$search}%"));
                });
            })
            ->orderBy('tanggal_jatuh_tempo')
            ->paginate(10, ['*'], 'active_page')
            ->withQueryString();

        $returnLogs = \App\Models\ReturnLog::query()
            ->with(['borrowing.member', 'receiver'])
            ->latest()
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        $completedReturns = \App\Models\BookReturn::query()
            ->with(['borrowing.member', 'receiver'])
            ->latest()
            ->paginate(10, ['*'], 'completed_page')
            ->withQueryString();

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

        return view('returns.create', compact('borrowing'));
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
            $returnedQtyByDetail
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
