<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookReturn;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Fine;
use App\Models\ReturnLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnService
{
    public function __construct(
        protected \App\Repositories\Interfaces\ReturnRepositoryInterface $returnRepository,
        protected FineService $fineService
    ) {}

    public function getActiveBorrowings(string $search = '')
    {
        return $this->returnRepository->getActiveBorrowings($search);
    }

    public function getReturnLogs()
    {
        return $this->returnRepository->getReturnLogs();
    }

    public function getCompletedReturns()
    {
        return $this->returnRepository->getCompletedReturns();
    }

    /**
     * @param  array<int, int>|null  $returnedQtyByDetail  [borrowing_detail_id => qty dikembalikan sekarang]
     */
    public function process(
        Borrowing $borrowing,
        User $receiver,
        ?Carbon $returnDate = null,
        ?array $returnedQtyByDetail = null,
        ?string $kondisiBuku = null,
        ?string $catatanKondisi = null
    ): ReturnProcessResult {
        return DB::transaction(function () use ($borrowing, $receiver, $returnDate, $returnedQtyByDetail, $kondisiBuku, $catatanKondisi) {
            $borrowing = Borrowing::query()
                ->with(['details.book', 'bookReturn', 'fine'])
                ->lockForUpdate()
                ->findOrFail($borrowing->id);

            if (! $borrowing->canBeReturned()) {
                throw ValidationException::withMessages([
                    'borrowing' => 'Peminjaman ini tidak dapat dikembalikan atau sudah lunas semua.',
                ]);
            }

            if ($borrowing->details->isEmpty()) {
                throw ValidationException::withMessages([
                    'borrowing' => 'Peminjaman tidak memiliki detail buku.',
                ]);
            }

            $returnDate = $returnDate ?? Carbon::today();
            $restoredBooks = [];
            $totalQtyKembali = 0;
            $dendaTambahan = 0;
            $ringkasanParts = [];

            foreach ($borrowing->details as $detail) {
                $qtySisa = $detail->qtySisa();

                if ($qtySisa <= 0) {
                    continue;
                }

                $qtyReturn = (int) ($returnedQtyByDetail[$detail->id] ?? $qtySisa);

                if ($qtyReturn <= 0) {
                    continue;
                }

                if ($qtyReturn > $qtySisa) {
                    throw ValidationException::withMessages([
                        'items' => "Jumlah kembali \"{$detail->book->judul}\" melebihi sisa ({$qtySisa} eksemplar).",
                    ]);
                }

                $book = Book::query()->whereKey($detail->book_id)->lockForUpdate()->firstOrFail();
                $stokSebelum = (int) $book->stok_tersedia;
                $stokSesudah = min((int) $book->jumlah_buku, $stokSebelum + $qtyReturn);

                $book->update(['stok_tersedia' => $stokSesudah]);

                $detail->update([
                    'qty_dikembalikan' => (int) $detail->qty_dikembalikan + $qtyReturn,
                ]);

                $dendaBaris = $this->fineService->calculateForReturnedQty(
                    $borrowing->tanggal_jatuh_tempo,
                    $returnDate,
                    $qtyReturn
                );
                $dendaTambahan += $dendaBaris;

                $totalQtyKembali += $qtyReturn;
                $ringkasanParts[] = "{$detail->book->judul} ({$qtyReturn} eks.)";

                $restoredBooks[] = [
                    'judul' => $detail->book->judul,
                    'qty' => $qtyReturn,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'sisa_setelah' => $detail->fresh()->qtySisa(),
                ];
            }

            if ($restoredBooks === []) {
                throw ValidationException::withMessages([
                    'items' => 'Masukkan jumlah kembali minimal 1 eksemplar untuk buku yang masih dipinjam.',
                ]);
            }

            ReturnLog::create([
                'borrowing_id' => $borrowing->id,
                'received_by' => $receiver->id,
                'tanggal_kembali' => $returnDate,
                'total_qty_kembali' => $totalQtyKembali,
                'ringkasan' => implode('; ', $ringkasanParts),
            ]);

            if ($dendaTambahan > 0) {
                $fine = Fine::firstOrCreate(
                    ['borrowing_id' => $borrowing->id],
                    [
                        'member_id' => $borrowing->member_id,
                        'jumlah_denda' => 0,
                        'status_bayar' => \App\Enums\FineStatus::BelumLunas,
                    ]
                );
                $fine->increment('jumlah_denda', $dendaTambahan);

                // Reset status bayar jika sebelumnya sudah lunas (ada denda baru)
                if ($fine->status_bayar === \App\Enums\FineStatus::Lunas) {
                    $fine->update(['status_bayar' => \App\Enums\FineStatus::BelumLunas]);
                }
            }

            $borrowing->refresh()->load('details');
            $isFullyReturned = $borrowing->isFullyReturned();
            $totalSisa = $borrowing->totalSisa();
            $bookReturn = null;

            if ($isFullyReturned) {
                $totalDenda = $borrowing->fine?->jumlah_denda ?? 0;

                $bookReturn = BookReturn::create([
                    'borrowing_id' => $borrowing->id,
                    'received_by' => $receiver->id,
                    'tanggal_kembali' => $returnDate,
                    'total_denda' => $totalDenda,
                    'kondisi_buku' => $kondisiBuku,
                    'catatan_kondisi' => $catatanKondisi,
                ]);

                $borrowing->update(['status' => \App\Enums\BorrowingStatus::Selesai]);
            }

            \Illuminate\Support\Facades\Log::info('Borrowing returned', [
                'borrowing_id' => $borrowing->id,
                'received_by' => $receiver->id,
                'is_fully_returned' => $isFullyReturned,
                'total_qty_kembali' => $totalQtyKembali,
            ]);

            return new ReturnProcessResult(
                restoredBooks: $restoredBooks,
                isFullyReturned: $isFullyReturned,
                totalSisa: $totalSisa,
                dendaTambahan: $dendaTambahan,
                bookReturn: $bookReturn,
            );
        });
    }
}
