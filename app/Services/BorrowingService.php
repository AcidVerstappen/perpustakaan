<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingService
{
    public function generateKodePinjam(): string
    {
        $prefix = 'PJ-'.now()->format('Ymd');
        $last = Borrowing::where('kode_pinjam', 'like', $prefix.'%')
            ->orderByDesc('kode_pinjam')
            ->value('kode_pinjam');

        $sequence = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function create(Member $member, array $items, ?User $processor = null): Borrowing
    {
        return DB::transaction(function () use ($member, $items, $processor) {
            $this->validateStockForItems($items, false);

            $today = Carbon::today();
            $loanDays = config('perpustakaan.hari_peminjaman', 7);

            $borrowing = Borrowing::create([
                'kode_pinjam' => $this->generateKodePinjam(),
                'member_id' => $member->id,
                'processed_by' => $processor?->id,
                'tanggal_pinjam' => $today,
                'tanggal_jatuh_tempo' => $today->copy()->addDays($loanDays),
                'status' => 'diajukan',
            ]);

            foreach ($items as $item) {
                $borrowing->details()->create([
                    'book_id' => $item['book_id'],
                    'qty' => $item['qty'],
                ]);
            }

            return $borrowing->load(['details.book', 'member']);
        });
    }

    public function approve(Borrowing $borrowing, User $processor): Borrowing
    {
        return DB::transaction(function () use ($borrowing, $processor) {
            if ($borrowing->status !== 'diajukan') {
                throw ValidationException::withMessages([
                    'status' => 'Hanya peminjaman berstatus diajukan yang dapat disetujui.',
                ]);
            }

            $borrowing->load('details.book');
            $items = $borrowing->details->map(fn ($d) => [
                'book_id' => $d->book_id,
                'qty' => $d->qty,
            ])->all();

            $this->validateStockForItems($items, true);

            foreach ($borrowing->details as $detail) {
                $book = Book::query()->whereKey($detail->book_id)->lockForUpdate()->firstOrFail();
                $qty = (int) $detail->qty;
                $newStock = max(0, (int) $book->stok_tersedia - $qty);
                $book->update(['stok_tersedia' => $newStock]);
            }

            $today = Carbon::today();
            $loanDays = config('perpustakaan.hari_peminjaman', 7);

            $borrowing->update([
                'status' => 'dipinjam',
                'processed_by' => $processor->id,
                'tanggal_pinjam' => $today,
                'tanggal_jatuh_tempo' => $today->copy()->addDays($loanDays),
            ]);

            return $borrowing->fresh(['details.book', 'member', 'processor']);
        });
    }

    public function reject(Borrowing $borrowing, User $processor): Borrowing
    {
        if ($borrowing->status !== 'diajukan') {
            throw ValidationException::withMessages([
                'status' => 'Hanya peminjaman berstatus diajukan yang dapat ditolak.',
            ]);
        }

        $borrowing->update([
            'status' => 'ditolak',
            'processed_by' => $processor->id,
        ]);

        return $borrowing->fresh();
    }

    public function markOverdueBorrowings(): int
    {
        return Borrowing::query()
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_jatuh_tempo', '<', Carbon::today())
            ->update(['status' => 'terlambat']);
    }

    protected function validateStockForItems(array $items, bool $lock): void
    {
        foreach ($items as $item) {
            $query = Book::query()->where('id', $item['book_id']);
            $book = $lock ? $query->lockForUpdate()->first() : $query->first();

            if (! $book) {
                throw ValidationException::withMessages([
                    'books' => 'Buku tidak ditemukan.',
                ]);
            }

            if ($book->stok_tersedia < $item['qty']) {
                throw ValidationException::withMessages([
                    'books' => "Stok buku \"{$book->judul}\" tidak mencukupi. Tersedia: {$book->stok_tersedia}, diminta: {$item['qty']}.",
                ]);
            }
        }
    }
}
