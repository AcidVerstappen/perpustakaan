@extends('layouts.admin')

@section('title', 'Anggota')
@section('page-title', 'Data Anggota')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Anggota</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">Daftar Anggota</h5>
            <a href="{{ route('members.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Anggota
            </a>
        </div>
        <div class="card-body">
            @include('partials.crud.search-bar', ['action' => route('members.index'), 'placeholder' => 'Cari NIS, nama, kelas...'])

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Jurusan</th>
                            <th>Akun</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            <tr>
                                <td>{{ $members->firstItem() + $loop->index }}</td>
                                <td><code>{{ $member->nis }}</code></td>
                                <td class="fw-semibold">{{ $member->nama }}</td>
                                <td>{{ $member->kelas }}</td>
                                <td>{{ $member->jurusan ?: '-' }}</td>
                                <td>
                                    @if ($member->user)
                                        <span class="badge text-bg-success">Ada</span>
                                        <small class="d-block text-muted">{{ $member->user->email }}</small>
                                    @else
                                        <span class="badge text-bg-secondary">Belum ada</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('members.destroy', $member) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus anggota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada data anggota.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $members->links() }}
        </div>
    </div>
@endsection
