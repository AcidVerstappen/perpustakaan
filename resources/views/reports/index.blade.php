@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan PDF')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-book fs-1 text-success"></i>
                    <h5 class="mt-3">Laporan Buku</h5>
                    <p class="text-muted small">Semua data buku perpustakaan</p>
                    <a href="{{ route('reports.books') }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 text-primary"></i>
                    <h5 class="mt-3">Laporan Anggota</h5>
                    <p class="text-muted small">Data seluruh anggota</p>
                    <a href="{{ route('reports.members') }}" class="btn btn-primary btn-sm" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-left-right fs-1 text-warning"></i>
                    <h5 class="mt-3">Laporan Peminjaman</h5>
                    <p class="text-muted small">Filter tanggal & status</p>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalBorrowings">
                        <i class="bi bi-funnel"></i> Filter & Unduh
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin fs-1 text-danger"></i>
                    <h5 class="mt-3">Laporan Denda</h5>
                    <p class="text-muted small">Filter status pembayaran</p>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalFines">
                        <i class="bi bi-funnel"></i> Filter & Unduh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBorrowings" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('reports.borrowings') }}" method="GET" target="_blank" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            @foreach (['diajukan','dipinjam','terlambat','selesai','ditolak'] as $s)
                                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning"><i class="bi bi-file-earmark-pdf"></i> Unduh PDF</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalFines" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('reports.fines') }}" method="GET" target="_blank" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporan Denda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status Bayar</label>
                        <select name="status_bayar" class="form-select">
                            <option value="">Semua</option>
                            <option value="belum_lunas">Belum Lunas</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> Unduh PDF</button>
                </div>
            </form>
        </div>
    </div>
@endsection
