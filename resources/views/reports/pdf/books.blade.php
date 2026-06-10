@extends('reports.pdf.layout')

@section('title', 'Laporan Buku')
@section('subtitle', 'Laporan Data Buku')

@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Penulis</th>
                <th class="text-center">Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($books as $i => $book)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $book->kode_buku }}</td>
                    <td>{{ $book->judul }}</td>
                    <td>{{ $book->category->nama_kategori }}</td>
                    <td>{{ $book->penulis }}</td>
                    <td class="text-center">{{ $book->stok_tersedia }}/{{ $book->jumlah_buku }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
