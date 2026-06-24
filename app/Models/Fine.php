<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = [
        'member_id',
        'borrowing_id',
        'jumlah_denda',
        'status_bayar',
        'tanggal_bayar',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_bayar' => 'date',
            'status_bayar' => \App\Enums\FineStatus::class,
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function isPaid(): bool
    {
        return $this->status_bayar === \App\Enums\FineStatus::Lunas;
    }

    public function statusLabel(): string
    {
        return $this->status_bayar->label();
    }

    public function statusBadgeClass(): string
    {
        return $this->status_bayar->badgeClass();
    }
}
