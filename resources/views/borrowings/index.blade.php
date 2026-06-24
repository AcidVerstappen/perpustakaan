@extends('layouts.admin')

@section('title', $isAdmin ? 'Peminjaman' : 'Riwayat Peminjaman')
@section('page-title', $isAdmin ? 'Data Peminjaman' : 'Riwayat Peminjaman Saya')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Peminjaman</li>
@endsection

@section('content')
    @if ($noMember ?? false)
        <div class="alert alert-warning">Profil anggota belum terhubung ke akun Anda. Hubungi admin perpustakaan.</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">Daftar Peminjaman</h5>
            @if ($isAdmin)
                <a href="{{ route('borrowings.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Peminjaman
                </a>
            @endif
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="search" name="search" class="form-control" placeholder="Cari kode / nama anggota..."
                           value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach (['diajukan','dipinjam','terlambat','selesai','ditolak'] as $s)
                            <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">Filter</button>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            @if ($isAdmin)<th>Anggota</th>@endif
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Buku</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($borrowings as $borrowing)
                            <tr>
                                <td><code>{{ $borrowing->kode_pinjam }}</code></td>
                                @if ($isAdmin)
                                    <td>{{ $borrowing->member->nama }}<br><small class="text-muted">{{ $borrowing->member->nis }}</small></td>
                                @endif
                                <td>{{ $borrowing->tanggal_pinjam->format('d/m/Y') }}</td>
                                <td>{{ $borrowing->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                                <td>{{ $borrowing->details_count }} judul</td>
                                <td>
                                    <span class="badge {{ $borrowing->statusBadgeClass() }}">{{ $borrowing->statusLabel() }}</span>
                                    @if ($borrowing->totalSisa() > 0 && in_array($borrowing->status->value, ['dipinjam', 'terlambat']))
                                        <br><small class="text-warning">Sisa {{ $borrowing->totalSisa() }} eks.</small>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('borrowings.show', $borrowing) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if ($borrowing->status->value === 'diajukan')
                                        <button type="button" onclick="showQrModal('{{ $borrowing->kode_pinjam }}', '{{ route('borrowings.qr', $borrowing) }}')" class="btn btn-sm btn-outline-dark" title="Tampilkan QR Booking">
                                            <i class="bi bi-qr-code"></i>
                                        </button>
                                    @endif
                                    @if ($isAdmin && $borrowing->status->value === 'diajukan')
                                        <form action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus peminjaman ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center text-muted py-4">Belum ada data peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $borrowings->links() }}
        </div>
    </div>

    <!-- QR Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrModalImage" src="" alt="QR Code" class="img-fluid border rounded mb-3" style="max-width: 200px;">
                    <div class="fw-bold text-success fs-5" id="qrModalKode"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showQrModal(kode, url) {
        document.getElementById('qrModalKode').innerText = kode;
        document.getElementById('qrModalImage').src = url;
        var myModal = new bootstrap.Modal(document.getElementById('qrModal'));
        myModal.show();
    }
</script>
@endpush
