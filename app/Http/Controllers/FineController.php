<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    public function __construct(protected \App\Services\FineService $fineService)
    {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();
        $status = $request->string('status')->toString();
        $user = $request->user();

        $fines = $this->fineService->getFines($search, $status, $user);

        $noMember = false;
        if ($user->hasRole('Siswa')) {
            $noMember = !$user->member;
        }

        $totalBelumLunas = $this->fineService->getTotalBelumLunas($user);

        return view('fines.index', [
            'fines' => $fines,
            'search' => $search,
            'status' => $status,
            'isAdmin' => $user->isAdminLibrary(),
            'noMember' => $noMember,
            'totalBelumLunas' => $totalBelumLunas,
        ]);
    }

    public function show(Fine $fine): View
    {
        $this->authorizeFineAccess($fine);

        $fine->load(['member', 'borrowing.details.book', 'borrowing.bookReturn']);

        return view('fines.show', [
            'fine' => $fine,
            'isAdmin' => auth()->user()->isAdminLibrary(),
        ]);
    }

    public function pay(Fine $fine): RedirectResponse
    {
        try {
            $this->fineService->pay($fine, auth()->user());
            return back()->with('success', 'Pembayaran denda berhasil dicatat.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function authorizeFineAccess(Fine $fine): void
    {
        $user = auth()->user();

        if ($user->isAdminLibrary()) {
            return;
        }

        if ($user->hasRole('Siswa') && $user->member?->id === $fine->member_id) {
            return;
        }

        abort(403);
    }
}
