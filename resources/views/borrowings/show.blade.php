@extends('layouts.admin')

@section('title', $borrowing->kode_pinjam)
@section('page-title', 'Detail Peminjaman')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('borrowings.index') }}">Peminjaman</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between">
                    <h5 class="mb-0">{{ $borrowing->kode_pinjam }}</h5>
                    <span class="badge {{ $borrowing->statusBadgeClass() }}">{{ $borrowing->statusLabel() }}</span>
                </div>
                <div class="card-body">
                        <div class="text-center mb-4">
                            <img src="{{ route('borrowings.qr', $borrowing) }}" alt="QR Code" class="img-fluid border rounded" style="max-width: 150px;">
                            <div class="mt-2 fw-bold text-success">{{ $borrowing->kode_pinjam }}</div>
                        </div>
                    <table class="table table-sm table-borderless">
                        <tr><th>Anggota</th><td>{{ $borrowing->member->nama }} ({{ $borrowing->member->nis }})</td></tr>
                        <tr><th>Kelas</th><td>{{ $borrowing->member->kelas }}</td></tr>
                        <tr><th>Tgl Pinjam</th><td>{{ $borrowing->tanggal_pinjam->format('d F Y') }}</td></tr>
                        <tr><th>Jatuh Tempo</th><td>{{ $borrowing->tanggal_jatuh_tempo->format('d F Y') }}</td></tr>
                        @if ($borrowing->tanggal_batas_ambil)
                            <tr><th>Batas Ambil</th><td><span class="text-danger fw-bold">{{ $borrowing->tanggal_batas_ambil->format('d F Y H:i') }}</span></td></tr>
                        @endif
                        @if ($borrowing->processor)
                            <tr><th>Diproses oleh</th><td>{{ $borrowing->processor->name }}</td></tr>
                        @endif
                        @if (in_array($borrowing->status->value, ['dipinjam', 'terlambat']))
                            <tr>
                                <th>Progress Kembali</th>
                                <td>
                                    {{ $borrowing->totalDikembalikan() }} / {{ $borrowing->totalDipinjam() }} eksemplar
                                    @if ($borrowing->totalSisa() > 0)
                                        — <span class="text-warning fw-bold">sisa {{ $borrowing->totalSisa() }} belum dikembalikan</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if ($borrowing->bookReturn)
                            <tr><th>Tgl Kembali</th><td>{{ $borrowing->bookReturn->tanggal_kembali->format('d F Y') }}</td></tr>
                            <tr><th>Total Denda</th><td>Rp{{ number_format($borrowing->bookReturn->total_denda, 0, ',', '.') }}</td></tr>
                        @endif
                    </table>

                    @if ($isAdmin && $borrowing->status->value === 'diajukan')
                        <div class="d-flex gap-2 mt-3">
                            <form action="{{ route('borrowings.approve', $borrowing) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Setujui peminjaman? Stok akan berkurang.')">
                                    <i class="bi bi-check-lg"></i> Setujui
                                </button>
                            </form>
                            <form action="{{ route('borrowings.reject', $borrowing) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Tolak peminjaman ini?')">
                                    <i class="bi bi-x-lg"></i> Tolak
                                </button>
                            </form>
                        </div>
                    @endif

                    @if ($isAdmin && $borrowing->canBeReturned())
                        <a href="{{ route('returns.create', $borrowing) }}" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-box-arrow-in-left"></i> Proses Pengembalian
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h5 class="mb-0">Detail Buku</h5></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th class="text-center">Pinjam</th>
                                <th class="text-center">Kembali</th>
                                <th class="text-center">Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($borrowing->details as $detail)
                                <tr>
                                    <td><code>{{ $detail->book->kode_buku }}</code></td>
                                    <td>{{ $detail->book->judul }}</td>
                                    <td class="text-center">{{ $detail->qty }}</td>
                                    <td class="text-center">{{ $detail->qty_dikembalikan }}</td>
                                    <td class="text-center">
                                        @if ($detail->qtySisa() > 0)
                                            <span class="badge text-bg-warning">{{ $detail->qtySisa() }}</span>
                                        @else
                                            <span class="text-success">0</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($borrowing->returnLogs->isNotEmpty())
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white"><h6 class="mb-0">Riwayat Pengembalian</h6></div>
                    <ul class="list-group list-group-flush small">
                        @foreach ($borrowing->returnLogs->sortByDesc('created_at') as $log)
                            <li class="list-group-item">
                                {{ $log->tanggal_kembali->format('d/m/Y') }} —
                                {{ $log->total_qty_kembali }} eks. oleh {{ $log->receiver->name }}<br>
                                <span class="text-muted">{{ $log->ringkasan }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($borrowing->fine)
                <div class="card border-0 shadow-sm mt-3 border-warning">
                    <div class="card-body">
                        <h6><i class="bi bi-cash-coin text-warning"></i> Informasi Denda</h6>
                        <p class="mb-1">Jumlah: <strong>Rp{{ number_format($borrowing->fine->jumlah_denda, 0, ',', '.') }}</strong></p>
                        <p class="mb-0">Status:
                            <span class="badge {{ $borrowing->fine->isPaid() ? 'text-bg-success' : 'text-bg-danger' }}">
                                {{ $borrowing->fine->isPaid() ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                        </p>
                        <a href="{{ route('fines.show', $borrowing->fine) }}" class="btn btn-sm btn-outline-warning mt-2">Lihat Detail Denda</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
