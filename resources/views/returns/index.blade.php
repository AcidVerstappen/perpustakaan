@extends('layouts.admin')

@section('title', 'Pengembalian')
@section('page-title', 'Pengembalian Buku')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengembalian</li>
@endsection

@section('content')
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
        <div>
            Denda keterlambatan: <strong>Rp{{ number_format(config('perpustakaan.denda_per_buku_per_hari', 1000), 0, ',', '.') }}</strong> per buku per hari.<br>
            Pengembalian bisa <strong>sebagian</strong>; kolom <em>Sisa</em> menampilkan buku yang belum dikembalikan.
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <h5 class="mb-3 text-warning fw-bold"><i class="bi bi-clock-history me-2"></i>Menunggu Pengembalian</h5>
            @include('partials.crud.search-bar', ['action' => route('returns.index'), 'placeholder' => 'Cari kode pinjam / nama...'])
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode Pinjam</th>
                            <th>Anggota</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center">Pinjam</th>
                            <th class="text-center">Kembali</th>
                            <th class="text-center">Sisa</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeBorrowings as $borrowing)
                            <tr class="{{ $borrowing->status->value === 'terlambat' ? 'table-danger' : '' }}">
                                <td class="ps-4"><code>{{ $borrowing->kode_pinjam }}</code></td>
                                <td class="text-nowrap">{{ $borrowing->member->nama }}</td>
                                <td class="text-nowrap">{{ $borrowing->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $borrowing->totalDipinjam() }}</td>
                                <td class="text-center text-success fw-bold">{{ $borrowing->totalDikembalikan() }}</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill text-bg-warning px-3">{{ $borrowing->totalSisa() }}</span>
                                </td>
                                <td><span class="badge {{ $borrowing->statusBadgeClass() }}">{{ $borrowing->statusLabel() }}</span></td>
                                <td class="text-end pe-4 text-nowrap">
                                    <a href="{{ route('returns.create', $borrowing) }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-box-arrow-in-left me-1"></i> Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-5">Tidak ada peminjaman aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($activeBorrowings->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3 pb-2">
            {{ $activeBorrowings->appends(request()->except('active_page'))->links() }}
        </div>
        @endif
    </div>

    @if ($returnLogs->total() > 0)
        <div class="card border-0 shadow-sm mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0 fw-bold"><i class="bi bi-journal-arrow-down me-2 text-info"></i>Pengembalian Sebagian Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Kode Pinjam</th>
                                <th>Anggota</th>
                                <th>Tanggal</th>
                                <th class="text-center">Qty</th>
                                <th>Ringkasan</th>
                                <th class="pe-4">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returnLogs as $log)
                                <tr>
                                    <td class="ps-4"><code>{{ $log->borrowing->kode_pinjam }}</code></td>
                                    <td class="text-nowrap">{{ $log->borrowing->member->nama }}</td>
                                    <td class="text-nowrap">{{ $log->tanggal_kembali->format('d/m/Y') }}</td>
                                    <td class="text-center fw-bold">{{ $log->total_qty_kembali }}</td>
                                    <td class="small text-muted">{{ $log->ringkasan }}</td>
                                    <td class="pe-4 text-nowrap">{{ $log->receiver->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($returnLogs->hasPages())
            <div class="card-footer bg-white border-top-0 pt-3 pb-2">
                {{ $returnLogs->appends(request()->except('history_page'))->links() }}
            </div>
            @endif
        </div>
    @endif

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white border-bottom-0 py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-check-circle-fill me-2 text-success"></i>Peminjaman Selesai (Lunas)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode Pinjam</th>
                            <th>Anggota</th>
                            <th>Tgl Selesai</th>
                            <th>Denda</th>
                            <th class="pe-4">Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($completedReturns as $return)
                            <tr>
                                <td class="ps-4"><code>{{ $return->borrowing->kode_pinjam }}</code></td>
                                <td class="text-nowrap">{{ $return->borrowing->member->nama }}</td>
                                <td class="text-nowrap">{{ $return->tanggal_kembali->format('d/m/Y') }}</td>
                                <td class="text-nowrap">
                                    @if($return->total_denda > 0)
                                        <span class="text-danger fw-bold">Rp{{ number_format($return->total_denda, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-success fw-bold">Rp0</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-nowrap">{{ $return->receiver->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-5">Belum ada peminjaman yang lunas dikembalikan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($completedReturns->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3 pb-2">
            {{ $completedReturns->appends(request()->except('completed_page'))->links() }}
        </div>
        @endif
    </div>
@endsection
