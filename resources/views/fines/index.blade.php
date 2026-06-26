@extends('layouts.admin')

@section('title', $isAdmin ? 'Denda' : 'Denda Saya')
@section('page-title', $isAdmin ? 'Data Denda' : 'Data Denda Saya')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Denda</li>
@endsection

@section('content')
    @if ($noMember ?? false)
        <div class="alert alert-warning">Profil anggota belum terhubung ke akun Anda.</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <h6 class="opacity-75">Total Denda Belum Lunas</h6>
                    <h3 class="mb-0 fw-bold">Rp{{ number_format($totalBelumLunas, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><h5 class="mb-0">Daftar Denda</h5></div>
        <div class="card-body">
            @if ($isAdmin)
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" name="search" class="form-control" placeholder="Cari nama / NIS..." value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas" @selected($status === 'belum_lunas')>Belum Lunas</option>
                        <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">Filter</button>
                </div>
            </form>
            @else
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="belum_lunas" @selected($status === 'belum_lunas')>Belum Lunas</option>
                        <option value="lunas" @selected($status === 'lunas')>Lunas</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">Filter</button>
                </div>
            </form>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            @if ($isAdmin)<th>Anggota</th>@endif
                            <th>Kode Pinjam</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Tgl Bayar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($fines as $fine)
                            <tr>
                                <td>{{ $fines->firstItem() + $loop->index }}</td>
                                @if ($isAdmin)
                                    <td>{{ $fine->member->nama }}<br><small class="text-muted">{{ $fine->member->nis }}</small></td>
                                @endif
                                <td><code>{{ $fine->borrowing->kode_pinjam }}</code></td>
                                <td class="fw-semibold">Rp{{ number_format($fine->jumlah_denda, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $fine->isPaid() ? 'text-bg-success' : 'text-bg-danger' }}">
                                        {{ $fine->isPaid() ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                </td>
                                <td>{{ $fine->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('fines.show', $fine) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center text-muted py-4">Tidak ada data denda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $fines->links() }}
        </div>
    </div>
@endsection
