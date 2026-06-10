@extends('layouts.admin')

@section('title', 'Detail Denda')
@section('page-title', 'Detail Denda')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between">
            <h5 class="mb-0">Denda Peminjaman {{ $fine->borrowing->kode_pinjam }}</h5>
            <span class="badge {{ $fine->isPaid() ? 'text-bg-success' : 'text-bg-danger' }} fs-6">
                {{ $fine->isPaid() ? 'Lunas' : 'Belum Lunas' }}
            </span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Anggota:</strong> {{ $fine->member->nama }} ({{ $fine->member->nis }})</p>
                    <p><strong>Jumlah Denda:</strong> <span class="fs-4 text-danger fw-bold">Rp{{ number_format($fine->jumlah_denda, 0, ',', '.') }}</span></p>
                    <p><strong>Tanggal Bayar:</strong> {{ $fine->tanggal_bayar?->format('d F Y') ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tgl Pinjam:</strong> {{ $fine->borrowing->tanggal_pinjam->format('d/m/Y') }}</p>
                    <p><strong>Jatuh Tempo:</strong> {{ $fine->borrowing->tanggal_jatuh_tempo->format('d/m/Y') }}</p>
                    @if ($fine->borrowing->bookReturn)
                        <p><strong>Tgl Kembali:</strong> {{ $fine->borrowing->bookReturn->tanggal_kembali->format('d/m/Y') }}</p>
                    @endif
                </div>
            </div>

            @if ($isAdmin && ! $fine->isPaid())
                <form action="{{ route('fines.pay', $fine) }}" method="POST" class="mt-3">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Catat denda sebagai lunas?')">
                        <i class="bi bi-check-circle"></i> Tandai Lunas
                    </button>
                </form>
            @endif

            <a href="{{ route('fines.index') }}" class="btn btn-outline-secondary mt-3">Kembali</a>
        </div>
    </div>
@endsection
