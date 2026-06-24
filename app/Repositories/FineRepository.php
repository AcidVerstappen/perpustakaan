<?php

namespace App\Repositories;

use App\Models\Fine;
use App\Models\User;
use App\Repositories\Interfaces\FineRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FineRepository implements FineRepositoryInterface
{
    public function getAll(string $search = '', string $status = '', ?User $user = null): LengthAwarePaginator
    {
        $query = Fine::query()
            ->with(['member', 'borrowing'])
            ->latest();

        if ($user && $user->hasRole('Siswa')) {
            $member = $user->member;
            if (! $member) {
                $query->whereRaw('0 = 1');
            } else {
                $query->where('member_id', $member->id);
            }
        }

        $query->when(!empty($search), function ($q) use ($search) {
            $q->whereHas('member', fn ($m) => $m->where('nama', 'like', "%{$search}%")
                ->orWhere('nis', 'like', "%{$search}%"));
        });

        $query->when($status !== '', fn ($q) => $q->where('status_bayar', $status));

        return $query->paginate(10)->withQueryString();
    }

    public function getTotalBelumLunas(?User $user = null): int
    {
        $totalQuery = Fine::query()->where('status_bayar', \App\Enums\FineStatus::BelumLunas);
        if ($user && $user->hasRole('Siswa') && $user->member) {
            $totalQuery->where('member_id', $user->member->id);
        }
        return $totalQuery->sum('jumlah_denda');
    }

    public function findById(int $id): ?Fine
    {
        return Fine::find($id);
    }

    public function updateStatus(Fine $fine, string $status, ?string $tanggalBayar = null): bool
    {
        return $fine->update([
            'status_bayar' => $status,
            'tanggal_bayar' => $tanggalBayar,
        ]);
    }
}
