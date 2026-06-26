@extends('layouts.admin')

@section('title', $book->judul)
@section('page-title', 'Detail Buku')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('books.index') }}">Buku</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    @if ($book->cover_url)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->judul }}" class="img-fluid rounded mb-3" style="max-height: 280px;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                            <i class="bi bi-book text-muted" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    @if ($book->isAvailable())
                        <span class="badge text-bg-success fs-6">Tersedia</span>
                    @else
                        <span class="badge text-bg-danger fs-6">Stok Habis</span>
                    @endif

                    @if ($isAdmin)
                        <div class="mt-3">
                            <button type="button" onclick="showQrModal()" class="btn btn-outline-dark btn-sm">
                                <i class="bi bi-qr-code me-1"></i> Tampilkan QR Code
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if ($isAdmin)
            <!-- QR Modal -->
            <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="qrModalLabel">QR Code Buku</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img src="{{ route('books.qr', $book) }}" alt="QR Code" class="img-fluid border rounded mb-3" style="max-width: 200px;">
                            <div class="fw-bold">{{ $book->kode_buku }}</div>
                            <div class="small text-muted">{{ $book->judul }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $book->judul }}</h5>
                    @if ($isAdmin)
                        <div class="d-flex gap-2">
                            <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th width="160">Kode Buku</th><td><code>{{ $book->kode_buku }}</code></td></tr>
                        <tr><th>ISBN</th><td>{{ $book->isbn ?: '-' }}</td></tr>
                        <tr><th>Penulis</th><td>{{ $book->penulis }}</td></tr>
                        <tr><th>Penerbit</th><td>{{ $book->penerbit ?: '-' }}</td></tr>
                        <tr><th>Tahun Terbit</th><td>{{ $book->tahun_terbit ?: '-' }}</td></tr>
                        <tr><th>Kategori</th><td>{{ $book->category->nama_kategori }}</td></tr>
                        <tr><th>Rak</th><td>{{ $book->shelf->kode_rak }} - {{ $book->shelf->nama_rak }}</td></tr>
                        <tr><th>Stok</th><td>{{ $book->stok_tersedia }} tersedia dari {{ $book->jumlah_buku }} eksemplar</td></tr>
                    </table>
                    @if ($book->deskripsi)
                        <hr>
                        <h6>Deskripsi</h6>
                        <p class="text-muted mb-0">{{ $book->deskripsi }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@if ($isAdmin)
@push('scripts')
<script>
    function showQrModal() {
        var myModal = new bootstrap.Modal(document.getElementById('qrModal'));
        myModal.show();
    }
</script>
@endpush
@endif
