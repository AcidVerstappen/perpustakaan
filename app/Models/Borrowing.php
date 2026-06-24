<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrowing extends Model
{
    protected $fillable = [
        'kode_pinjam',
        'member_id',
        'processed_by',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'status',
        'tanggal_batas_ambil',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'tanggal_batas_ambil' => 'datetime',
            'status' => \App\Enums\BorrowingStatus::class,
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function bookReturn(): HasOne
    {
        return $this->hasOne(BookReturn::class);
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function returnLogs(): HasMany
    {
        return $this->hasMany(ReturnLog::class);
    }

    public function totalDipinjam(): int
    {
        return (int) $this->details->sum('qty');
    }

    public function totalDikembalikan(): int
    {
        return (int) $this->details->sum('qty_dikembalikan');
    }

    public function totalSisa(): int
    {
        return (int) $this->details->reduce(
            fn (int $total, BorrowingDetail $detail) => $total + $detail->qtySisa(),
            0
        );
    }

    public function isFullyReturned(): bool
    {
        if ($this->details->isEmpty()) {
            return false;
        }

        return $this->details->every(fn (BorrowingDetail $d) => $d->qtySisa() === 0);
    }

    public function hasPartialReturn(): bool
    {
        return $this->totalDikembalikan() > 0 && ! $this->isFullyReturned();
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, [\App\Enums\BorrowingStatus::Dipinjam, \App\Enums\BorrowingStatus::Terlambat], true)
            && $this->tanggal_jatuh_tempo->isPast();
    }

    public function canBeReturned(): bool
    {
        return in_array($this->status, [\App\Enums\BorrowingStatus::Dipinjam, \App\Enums\BorrowingStatus::Terlambat], true)
            && $this->totalSisa() > 0;
    }

    public function statusLabel(): string
    {
        if ($this->hasPartialReturn()) {
            return 'Dikembalikan Sebagian';
        }

        return $this->status->label();
    }

    public function statusBadgeClass(): string
    {
        if ($this->hasPartialReturn()) {
            return 'text-bg-info';
        }

        return $this->status->badgeClass();
    }
}
