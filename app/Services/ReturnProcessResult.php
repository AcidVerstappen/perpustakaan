<?php

namespace App\Services;

use App\Models\BookReturn;

class ReturnProcessResult
{
    /**
     * @param  array<int, array{judul: string, qty: int, stok_sebelum: int, stok_sesudah: int}>  $restoredBooks
     */
    public function __construct(
        public array $restoredBooks,
        public bool $isFullyReturned,
        public int $totalSisa,
        public int $dendaTambahan,
        public ?BookReturn $bookReturn = null,
    ) {}
}
