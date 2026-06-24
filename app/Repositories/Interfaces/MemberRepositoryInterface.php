<?php

namespace App\Repositories\Interfaces;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MemberRepositoryInterface
{
    public function getAll(string $search = null): LengthAwarePaginator;
    public function create(array $data): Member;
    public function update(Member $member, array $data): bool;
    public function delete(Member $member): bool;
}
