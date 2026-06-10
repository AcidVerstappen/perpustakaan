<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();
        $status = $request->string('status')->toString();

        $query = Fine::query()
            ->with(['member', 'borrowing'])
            ->latest();

        $noMember = false;
        if ($request->user()->hasRole('Siswa')) {
            $member = $request->user()->member;
            if (! $member) {
                $noMember = true;
                $query->whereRaw('0 = 1');
            } else {
                $query->where('member_id', $member->id);
            }
        }

        $query->when($search->isNotEmpty(), function ($q) use ($search) {
            $q->whereHas('member', fn ($m) => $m->where('nama', 'like', "%{$search}%")
                ->orWhere('nis', 'like', "%{$search}%"));
        });

        $query->when($status !== '', fn ($q) => $q->where('status_bayar', $status));

        $fines = $query->paginate(10)->withQueryString();

        $totalQuery = Fine::query()->where('status_bayar', 'belum_lunas');
        if ($request->user()->hasRole('Siswa') && $request->user()->member) {
            $totalQuery->where('member_id', $request->user()->member->id);
        }
        $totalBelumLunas = $totalQuery->sum('jumlah_denda');

        return view('fines.index', [
            'fines' => $fines,
            'search' => $search,
            'status' => $status,
            'isAdmin' => $request->user()->isAdminLibrary(),
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
        if (! auth()->user()->isAdminLibrary()) {
            abort(403);
        }

        if ($fine->isPaid()) {
            return back()->with('error', 'Denda sudah lunas.');
        }

        $fine->update([
            'status_bayar' => 'lunas',
            'tanggal_bayar' => now(),
        ]);

        return back()->with('success', 'Pembayaran denda berhasil dicatat.');
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
