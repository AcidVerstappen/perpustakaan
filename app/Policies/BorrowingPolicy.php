<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BorrowingPolicy
{

    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdminLibrary()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        return $user->member?->id === $borrowing->member_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Siswa');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Borrowing $borrowing): bool
    {
        // In this context, approving/rejecting is a form of update.
        // The more specific methods below will be used.
        return false;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Borrowing $borrowing): bool
    {
        return $borrowing->status === 'diajukan';
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, Borrowing $borrowing): bool
    {
        return $borrowing->status === 'diajukan';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Borrowing $borrowing): bool
    {
        return $borrowing->status === 'diajukan';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Borrowing $borrowing): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Borrowing $borrowing): bool
    {
        return false;
    }
}

