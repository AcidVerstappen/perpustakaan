@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    @if ($isAdmin)
        <div class="alert alert-warning border-0 shadow-sm">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Password default (<code>password</code>) hanya untuk development lokal. Wajib diganti di production.
        </div>
    @endif

    @role('Siswa')
        @if (auth()->user()->member)
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card stat-card bg-primary position-relative">
                        <div class="card-body">
                            <h3 class="mb-0 fw-bold">{{ $stats['my_borrowings'] ?? 0 }}</h3>
                            <p class="mb-0 opacity-75">Total Peminjaman Saya</p>
                            <i class="bi bi-arrow-left-right stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-warning position-relative">
                        <div class="card-body">
                            <h3 class="mb-0 fw-bold">{{ $stats['my_active'] ?? 0 }}</h3>
                            <p class="mb-0 opacity-75">Peminjaman Aktif</p>
                            <i class="bi bi-clock stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-danger position-relative">
                        <div class="card-body">
                            <h3 class="mb-0 fw-bold">Rp{{ number_format($stats['my_fines'] ?? 0, 0, ',', '.') }}</h3>
                            <p class="mb-0 opacity-75">Denda Belum Lunas</p>
                            <i class="bi bi-cash-coin stat-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">Akun Anda belum terhubung ke data anggota. Hubungi admin perpustakaan.</div>
        @endif
    @endrole

    @if ($isAdmin)
        <div class="row g-3 mb-4">
            @foreach ([
                ['color' => 'primary', 'icon' => 'bi-people', 'label' => 'Anggota', 'value' => $stats['members']],
                ['color' => 'success', 'icon' => 'bi-book', 'label' => 'Buku', 'value' => $stats['books']],
                ['color' => 'warning', 'icon' => 'bi-arrow-left-right', 'label' => 'Peminjaman Aktif', 'value' => $stats['borrowings_active']],
                ['color' => 'danger', 'icon' => 'bi-cash-coin', 'label' => 'Denda Belum Lunas', 'value' => $stats['fines']],
            ] as $stat)
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card bg-{{ $stat['color'] }} position-relative">
                        <div class="card-body">
                            <h3 class="mb-0 fw-bold">{{ $stat['value'] }}</h3>
                            <p class="mb-0 opacity-75">{{ $stat['label'] }}</p>
                            <i class="bi {{ $stat['icon'] }} stat-icon"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <span class="text-muted small">Total Denda Belum Lunas</span>
                        <h4 class="text-danger mb-0">Rp{{ number_format($stats['fines_total'], 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <span class="text-muted small">Total Semua Peminjaman</span>
                        <h4 class="mb-0">{{ $stats['borrowings'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="bi bi-book me-2 text-success"></i>Buku Terbaru</h5>
                    <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-success">Lihat</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Judul</th><th>Stok</th></tr></thead>
                        <tbody>
                            @forelse ($recentBooks as $book)
                                <tr>
                                    <td><a href="{{ route('books.show', $book) }}">{{ Str::limit($book->judul, 35) }}</a></td>
                                    <td><span class="badge {{ $book->isAvailable() ? 'text-bg-success' : 'text-bg-danger' }}">{{ $book->stok_tersedia }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">Belum ada buku.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right me-2 text-warning"></i>Peminjaman Terbaru</h5>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-outline-warning">Lihat</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Kode</th>@if($isAdmin)<th>Anggota</th>@endif<th>Status</th></tr></thead>
                        <tbody>
                            @forelse ($recentBorrowings as $b)
                                <tr>
                                    <td><a href="{{ route('borrowings.show', $b) }}"><code>{{ $b->kode_pinjam }}</code></a></td>
                                    @if($isAdmin)<td>{{ $b->member->nama }}</td>@endif
                                    <td><span class="badge {{ $b->statusBadgeClass() }}">{{ $b->statusLabel() }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $isAdmin ? 3 : 2 }}" class="text-center text-muted py-3">Belum ada peminjaman.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($isAdmin && $overdueBorrowings->isNotEmpty())
            <div class="col-12">
                <div class="card border-0 shadow-sm border-danger">
                    <div class="card-header bg-white text-danger">
                        <h5 class="mb-0"><i class="bi bi-exclamation-octagon me-2"></i>Peminjaman Terlambat</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light"><tr><th>Kode</th><th>Anggota</th><th>Jatuh Tempo</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($overdueBorrowings as $b)
                                    <tr>
                                        <td><code>{{ $b->kode_pinjam }}</code></td>
                                        <td>{{ $b->member->nama }}</td>
                                        <td>{{ $b->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                                        <td><a href="{{ route('returns.create', $b) }}" class="btn btn-sm btn-success">Kembalikan</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @if ($isAdmin && $lowStockBooks->isNotEmpty())
            <div class="col-12">
                <div class="card border-0 shadow-sm border-warning">
                    <div class="card-header bg-white"><h5 class="mb-0 text-warning"><i class="bi bi-exclamation-circle me-2"></i>Stok Menipis</h5></div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light"><tr><th>Kode</th><th>Judul</th><th>Stok</th></tr></thead>
                            <tbody>
                                @foreach ($lowStockBooks as $book)
                                    <tr>
                                        <td><code>{{ $book->kode_buku }}</code></td>
                                        <td>{{ $book->judul }}</td>
                                        <td><span class="badge text-bg-warning">{{ $book->stok_tersedia }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
