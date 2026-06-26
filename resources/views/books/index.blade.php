@extends('layouts.admin')

@section('title', $isAdmin ? 'Buku' : 'Katalog Buku')
@section('page-title', $isAdmin ? 'Data Buku' : 'Katalog Buku')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Buku</li>
@endsection

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="mb-0">{{ $isAdmin ? 'Daftar Buku' : 'Katalog Buku Sekolah' }}</h5>
            @if ($isAdmin)
                <a href="{{ route('books.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Buku
                </a>
            @endif
        </div>
        <div class="card-body">
            @include('partials.crud.search-bar', [
                'action' => route('books.index'),
                'placeholder' => 'Cari judul, kode, penulis, ISBN...',
            ])

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Cover</th>
                            <th>Kode</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($books as $book)
                            <tr>
                                <td>{{ $books->firstItem() + $loop->index }}</td>
                                <td>
                                    @if ($book->cover_url)
                                        <img src="{{ $book->cover_url }}" alt="Cover" class="rounded" width="40" height="50" style="object-fit: cover;">
                                    @else
                                        <span class="text-muted"><i class="bi bi-image fs-4"></i></span>
                                    @endif
                                </td>
                                <td><code>{{ $book->kode_buku }}</code></td>
                                <td class="fw-semibold">{{ $book->judul }}</td>
                                <td>{{ $book->category->nama_kategori }}</td>
                                <td>{{ $book->penulis }}</td>
                                <td>{{ $book->stok_tersedia }} / {{ $book->jumlah_buku }}</td>
                                <td>
                                    @if ($book->isAvailable())
                                        <span class="badge text-bg-success">Tersedia</span>
                                    @else
                                        <span class="badge text-bg-danger">Habis</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if ($isAdmin)
                                        <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" onclick="showQrModal('{{ $book->kode_buku }}', '{{ $book->judul }}', '{{ route('books.qr', $book) }}')" class="btn btn-sm btn-outline-dark" title="Tampilkan QR">
                                            <i class="bi bi-qr-code"></i>
                                        </button>
                                        <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Hapus buku ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        @if($book->isAvailable())
                                        <form action="{{ route('booking.cart.add', $book) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Booking Buku">
                                                <i class="bi bi-cart-plus"></i> Booking
                                            </button>
                                        </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada data buku.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $books->links() }}
        </div>
    </div>

    <!-- QR Modal -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrModalLabel">QR Code Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrModalImage" src="" alt="QR Code" class="img-fluid border rounded mb-3" style="max-width: 200px;">
                    <div class="fw-bold" id="qrModalKode"></div>
                    <div class="small text-muted" id="qrModalJudul"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function showQrModal(kode, judul, url) {
        document.getElementById('qrModalKode').innerText = kode;
        document.getElementById('qrModalJudul').innerText = judul;
        document.getElementById('qrModalImage').src = url;
        var myModal = new bootstrap.Modal(document.getElementById('qrModal'));
        myModal.show();
    }
</script>
@endpush
