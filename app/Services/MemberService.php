<?php

namespace App\Services;

use App\Enums\BorrowingStatus;
use App\Models\Member;
use App\Models\User;
use App\Repositories\Interfaces\MemberRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class MemberService
{
    public function __construct(protected MemberRepositoryInterface $memberRepository)
    {
    }

    public function getAllMembers(string $search = null): LengthAwarePaginator
    {
        return $this->memberRepository->getAll($search);
    }

    public function createMember(array $data, bool $createAccount, array $accountData = []): Member
    {
        return DB::transaction(function () use ($data, $createAccount, $accountData) {
            if (isset($data['foto']) && $data['foto'] instanceof UploadedFile) {
                $data['foto'] = $data['foto']->store('members', 'public');
            }

            if ($createAccount) {
                $user = User::create([
                    'name' => $data['nama'],
                    'email' => $accountData['email'],
                    'password' => Hash::make($accountData['password']),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('Siswa');
                $data['user_id'] = $user->id;
            }

            return $this->memberRepository->create($data);
        });
    }

    public function updateMember(Member $member, array $data, bool $createAccount, array $accountData = []): bool
    {
        return DB::transaction(function () use ($member, $data, $createAccount, $accountData) {
            if (isset($data['foto']) && $data['foto'] instanceof UploadedFile) {
                if ($member->foto) {
                    Storage::disk('public')->delete($member->foto);
                }
                $data['foto'] = $data['foto']->store('members', 'public');
            }

            if ($createAccount && ! $member->user_id) {
                $user = User::create([
                    'name' => $data['nama'],
                    'email' => $accountData['email'],
                    'password' => Hash::make($accountData['password']),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('Siswa');
                $data['user_id'] = $user->id;
            } elseif ($member->user && !empty($accountData['email'])) {
                $member->user->update([
                    'name' => $data['nama'],
                    'email' => $accountData['email'],
                ]);
                if (!empty($accountData['password'])) {
                    $member->user->update(['password' => Hash::make($accountData['password'])]);
                }
            }

            return $this->memberRepository->update($member, $data);
        });
    }

    public function deleteMember(Member $member): bool
    {
        $activeCount = $member->borrowings()
            ->whereIn('status', [
                BorrowingStatus::Diajukan,
                BorrowingStatus::Dipinjam,
                BorrowingStatus::Terlambat,
            ])
            ->count();

        if ($activeCount > 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'member' => 'Anggota tidak dapat dihapus karena masih memiliki peminjaman aktif.',
            ]);
        }

        return DB::transaction(function () use ($member) {
            if ($member->foto) {
                Storage::disk('public')->delete($member->foto);
            }

            $user = $member->user;
            $result = $this->memberRepository->delete($member);

            if ($user && $user->email !== 'siswa@perpus.test') {
                $user->delete();
            }

            return $result;
        });
    }
}
