<?php

namespace App\Enums;

enum BorrowingStatus: string
{
    case Diajukan = 'diajukan';
    case Dipinjam = 'dipinjam';
    case Terlambat = 'terlambat';
    case Selesai = 'selesai';
    case Ditolak = 'ditolak';
    case Dipesan = 'dipesan';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match($this) {
            self::Diajukan => 'Diajukan',
            self::Dipinjam => 'Dipinjam',
            self::Terlambat => 'Terlambat',
            self::Selesai => 'Selesai',
            self::Ditolak => 'Ditolak',
            self::Dipesan => 'Dipesan',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Diajukan => 'text-bg-warning',
            self::Dipinjam => 'text-bg-primary',
            self::Terlambat => 'text-bg-danger',
            self::Selesai => 'text-bg-success',
            self::Ditolak => 'text-bg-secondary',
            self::Dipesan => 'text-bg-info',
            self::Dibatalkan => 'text-bg-dark',
        };
    }
}
