@extends('reports.pdf.layout')

@section('title', 'Laporan Denda')
@section('subtitle', 'Laporan Data Denda')

@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Anggota</th>
                <th>Kode Pinjam</th>
                <th class="text-end">Jumlah</th>
                <th>Status</th>
                <th>Tgl Bayar</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fines as $i => $fine)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $fine->member->nama }}</td>
                    <td>{{ $fine->borrowing->kode_pinjam }}</td>
                    <td class="text-end">Rp{{ number_format($fine->jumlah_denda, 0, ',', '.') }}</td>
                    <td>{{ $fine->isPaid() ? 'Lunas' : 'Belum Lunas' }}</td>
                    <td>{{ $fine->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
