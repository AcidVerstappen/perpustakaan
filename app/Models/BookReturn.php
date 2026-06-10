<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReturn extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'borrowing_id',
        'received_by',
        'tanggal_kembali',
        'total_denda',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kembali' => 'date',
        ];
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
