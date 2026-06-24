<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Fine;
use Carbon\Carbon;

class FineService
{
    public function __construct(protected \App\Repositories\Interfaces\FineRepositoryInterface $fineRepository)
    {
    }

    public function getFines(string $search = '', string $status = '', ?\App\Models\User $user = null)
    {
        return $this->fineRepository->getAll($search, $status, $user);
    }

    public function getTotalBelumLunas(?\App\Models\User $user = null): int
    {
        return $this->fineRepository->getTotalBelumLunas($user);
    }

    public function pay(Fine $fine, \App\Models\User $user): void
    {
        if (! $user->isAdminLibrary()) {
            abort(403);
        }

        if ($fine->isPaid()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'status_bayar' => 'Denda sudah lunas.',
            ]);
        }

        $this->fineRepository->updateStatus($fine, \App\Enums\FineStatus::Lunas->value, now());

        \Illuminate\Support\Facades\Log::info('Fine paid', [
            'fine_id' => $fine->id,
            'member_id' => $fine->member_id,
            'processed_by' => $user->id,
            'amount' => $fine->jumlah_denda,
        ]);
    }
    /**
     * @deprecated Use calculateForReturnedQty() so partial returns are fined accurately.
     */
    public function calculate(Borrowing $borrowing, Carbon $returnDate): int
    {
        if (! $returnDate->gt($borrowing->tanggal_jatuh_tempo)) {
            return 0;
        }

        $daysLate = $borrowing->tanggal_jatuh_tempo->diffInDays($returnDate);
        $totalBooks = $borrowing->details->sum('qty');
        $rate = config('perpustakaan.denda_per_buku_per_hari', 1000);

        return (int) ($daysLate * $totalBooks * $rate);
    }

    /** Denda untuk qty buku yang dikembalikan pada tanggal tertentu (pengembalian sebagian). */
    public function calculateForReturnedQty(Carbon $jatuhTempo, Carbon $returnDate, int $qty): int
    {
        if ($qty <= 0 || ! $returnDate->gt($jatuhTempo)) {
            return 0;
        }

        $daysLate = $jatuhTempo->diffInDays($returnDate);
        $rate = config('perpustakaan.denda_per_buku_per_hari', 1000);

        return (int) ($daysLate * $qty * $rate);
    }
}
