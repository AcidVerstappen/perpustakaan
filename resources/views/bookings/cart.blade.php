@extends('layouts.admin')

@section('title', 'Keranjang Booking')
@section('page-title', 'Keranjang Booking')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Keranjang Booking</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Keranjang Booking (Online Booking)</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Buku di Keranjang</h6>
        </div>
        <div class="card-body">
            @if(empty($cart))
                <div class="text-center py-4">
                    <p class="text-muted">Keranjang masih kosong.</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary">Cari Buku</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 1; @endphp
                            @foreach($cart as $bookId => $qty)
                                @php $book = $books[$bookId]; @endphp
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $book->judul }}</td>
                                    <td>{{ $qty }}</td>
                                    <td>
                                        <form action="{{ route('booking.cart.remove', $book) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-end">
                    <form action="{{ route('booking.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Selesaikan Booking</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
