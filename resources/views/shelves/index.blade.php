@extends('layouts.admin')

@section('title', 'Rak')
@section('page-title', 'Data Rak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Rak</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">Daftar Rak</h5>
            <a href="{{ route('shelves.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Rak
            </a>
        </div>
        <div class="card-body">
            @include('partials.crud.search-bar', ['action' => route('shelves.index'), 'placeholder' => 'Cari kode, nama, lokasi...'])

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode Rak</th>
                            <th>Nama Rak</th>
                            <th>Lokasi</th>
                            <th>Jumlah Buku</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shelves as $shelf)
                            <tr>
                                <td>{{ $shelves->firstItem() + $loop->index }}</td>
                                <td><span class="badge text-bg-dark">{{ $shelf->kode_rak }}</span></td>
                                <td class="fw-semibold">{{ $shelf->nama_rak }}</td>
                                <td>{{ $shelf->lokasi ?: '-' }}</td>
                                <td><span class="badge text-bg-secondary">{{ $shelf->books_count }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('shelves.edit', $shelf) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('shelves.destroy', $shelf) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus rak ini?')">
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
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data rak.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $shelves->links() }}
        </div>
    </div>
@endsection
