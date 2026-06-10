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
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim();

        $members = Member::query()
            ->with('user')
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('nis', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('kelas', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('members.index', compact('members', 'search'));
    }

    public function create(): View
    {
        return view('members.create');
    }

    public function store(MemberRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->safe()->except(['buat_akun', 'email', 'password', 'password_confirmation', 'foto']);

            if ($request->hasFile('foto')) {
                $data['foto'] = $request->file('foto')->store('members', 'public');
            }

            if ($request->boolean('buat_akun')) {
                $user = User::create([
                    'name' => $request->nama,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('Siswa');
                $data['user_id'] = $user->id;
            }

            Member::create($data);
        });

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
        DB::transaction(function () use ($request, $member) {
            $data = $request->safe()->except(['buat_akun', 'email', 'password', 'password_confirmation', 'foto']);

            if ($request->hasFile('foto')) {
                if ($member->foto) {
                    Storage::disk('public')->delete($member->foto);
                }
                $data['foto'] = $request->file('foto')->store('members', 'public');
            }

            if ($request->boolean('buat_akun') && ! $member->user_id) {
                $user = User::create([
                    'name' => $request->nama,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'email_verified_at' => now(),
                ]);
                $user->assignRole('Siswa');
                $data['user_id'] = $user->id;
            } elseif ($member->user && $request->filled('email')) {
                $member->user->update([
                    'name' => $request->nama,
                    'email' => $request->email,
                ]);
                if ($request->filled('password')) {
                    $member->user->update(['password' => Hash::make($request->password)]);
                }
            }

            $member->update($data);
        });

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        DB::transaction(function () use ($member) {
            if ($member->foto) {
                Storage::disk('public')->delete($member->foto);
            }

            $user = $member->user;
            $member->delete();

            if ($user && $user->email !== 'siswa@perpus.test') {
                $user->delete();
            }
        });

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
