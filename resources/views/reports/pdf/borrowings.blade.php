@extends('reports.pdf.layout')

@section('title', 'Laporan Peminjaman')
@section('subtitle', 'Laporan Peminjaman Buku')

@section('content')
    @if (! empty($filters['dari']) || ! empty($filters['sampai']) || ! empty($filters['status']))
        <p><strong>Filter:</strong>
            @if (! empty($filters['dari'])) Dari {{ $filters['dari'] }} @endif
            @if (! empty($filters['sampai'])) Sampai {{ $filters['sampai'] }} @endif
            @if (! empty($filters['status'])) Status: {{ $filters['status'] }} @endif
        </p>
    @endif
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Anggota</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($borrowings as $b)
                <tr>
                    <td>{{ $b->kode_pinjam }}</td>
                    <td>{{ $b->member->nama }}</td>
                    <td>{{ $b->tanggal_pinjam->format('d/m/Y') }}</td>
                    <td>{{ $b->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                    <td>{{ $b->statusLabel() }}</td>
                    <td>{{ $b->processor->name ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
@endsection
