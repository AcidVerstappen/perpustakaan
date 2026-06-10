@extends('layouts.admin')

@section('title', 'Pengembalian')
@section('page-title', 'Pengembalian Buku')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Pengembalian</li>
@endsection

@section('content')
    <div class="alert alert-info">
        Denda keterlambatan: <strong>Rp{{ number_format(config('perpustakaan.denda_per_buku_per_hari', 1000), 0, ',', '.') }}</strong> per buku per hari.
        Pengembalian bisa <strong>sebagian</strong>; kolom <em>Sisa</em> menampilkan buku yang belum dikembalikan.
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-warning"><i class="bi bi-clock-history me-2"></i>Menunggu Pengembalian</h5>
        </div>
        <div class="card-body">
            @include('partials.crud.search-bar', ['action' => route('returns.index'), 'placeholder' => 'Cari kode pinjam / nama...'])

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Pinjam</th>
                            <th>Anggota</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center">Pinjam</th>
                            <th class="text-center">Sudah Kembali</th>
                            <th class="text-center">Sisa</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeBorrowings as $borrowing)
                            <tr class="{{ $borrowing->status === 'terlambat' ? 'table-danger' : '' }}">
                                <td><code>{{ $borrowing->kode_pinjam }}</code></td>
                                <td>{{ $borrowing->member->nama }}</td>
                                <td>{{ $borrowing->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $borrowing->totalDipinjam() }}</td>
                                <td class="text-center">{{ $borrowing->totalDikembalikan() }}</td>
                                <td class="text-center">
                                    <span class="badge text-bg-warning">{{ $borrowing->totalSisa() }}</span>
                                </td>
                                <td><span class="badge {{ $borrowing->statusBadgeClass() }}">{{ $borrowing->statusLabel() }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('returns.create', $borrowing) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-box-arrow-in-left"></i> Kembalikan
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada peminjaman aktif.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $activeBorrowings->appends(request()->except('active_page'))->links() }}
        </div>
    </div>

    @if ($returnLogs->total() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-journal-arrow-down me-2 text-info"></i>Pengembalian Sebagian Terbaru</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Pinjam</th>
                            <th>Anggota</th>
                            <th>Tgl</th>
                            <th class="text-center">Qty</th>
                            <th>Ringkasan</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returnLogs as $log)
                            <tr>
                                <td><code>{{ $log->borrowing->kode_pinjam }}</code></td>
                                <td>{{ $log->borrowing->member->nama }}</td>
                                <td>{{ $log->tanggal_kembali->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $log->total_qty_kembali }}</td>
                                <td class="small text-muted">{{ $log->ringkasan }}</td>
                                <td>{{ $log->receiver->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $returnLogs->appends(request()->except('history_page'))->links() }}</div>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-check-circle me-2 text-success"></i>Peminjaman Selesai (Lunas)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Pinjam</th>
                        <th>Anggota</th>
                        <th>Tgl Kembali Terakhir</th>
                        <th>Denda</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($completedReturns as $return)
                        <tr>
                            <td><code>{{ $return->borrowing->kode_pinjam }}</code></td>
                            <td>{{ $return->borrowing->member->nama }}</td>
                            <td>{{ $return->tanggal_kembali->format('d/m/Y') }}</td>
                            <td>Rp{{ number_format($return->total_denda, 0, ',', '.') }}</td>
                            <td>{{ $return->receiver->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada peminjaman yang lunas dikembalikan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $completedReturns->appends(request()->except('completed_page'))->links() }}</div>
    </div>
@endsection
