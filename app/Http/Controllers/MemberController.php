<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(protected \App\Services\MemberService $memberService)
    {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();
        $members = $this->memberService->getAllMembers($search);

        return view('members.index', compact('members', 'search'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(MemberRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['buat_akun', 'email', 'password', 'password_confirmation', 'foto']);
        $accountData = $request->only(['email', 'password']);
        
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto');
        }

        $this->memberService->createMember($data, $request->boolean('buat_akun'), $accountData);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function edit(Member $member): View
    {
        $member->load('user');

        return view('members.edit', compact('member'));
    }

    public function update(MemberRequest $request, Member $member): RedirectResponse
    {
        $data = $request->safe()->except(['buat_akun', 'email', 'password', 'password_confirmation', 'foto']);
        $accountData = $request->only(['email', 'password']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto');
        }

        $this->memberService->updateMember($member, $data, $request->boolean('buat_akun'), $accountData);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->memberService->deleteMember($member);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
