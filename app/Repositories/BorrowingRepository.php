<?php

namespace App\Repositories;

use App\Models\Borrowing;
use App\Models\User;
use App\Repositories\Interfaces\BorrowingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BorrowingRepository implements BorrowingRepositoryInterface
{
    public function getAll(string $search = '', string $status = '', ?User $user = null): LengthAwarePaginator
    {
        $query = Borrowing::query()
            ->with(['member', 'processor', 'details.book'])
            ->withCount('details')
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
            $q->where(function ($inner) use ($search) {
                $inner->where('kode_pinjam', 'like', "%{$search}%")
                    ->orWhereHas('member', fn ($m) => $m->where('nama', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%"));
            });
        });

        $query->when($status !== '', fn ($q) => $q->where('status', $status));

        return $query->paginate(10)->withQueryString();
    }

    public function findById(int $id): ?Borrowing
    {
        return Borrowing::find($id);
    }
}
