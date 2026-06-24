<?php

namespace App\Enums;

enum FineStatus: string
{
    case Lunas = 'lunas';
    case BelumLunas = 'belum_lunas';

    public function label(): string
    {
        return match($this) {
            self::Lunas => 'Lunas',
            self::BelumLunas => 'Belum Lunas',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::Lunas => 'text-bg-success',
            self::BelumLunas => 'text-bg-danger',
        };
    }
}
