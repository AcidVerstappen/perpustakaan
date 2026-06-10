<?php

namespace App\Services;

use App\Models\Borrowing;
use Carbon\Carbon;

class FineService
{
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
