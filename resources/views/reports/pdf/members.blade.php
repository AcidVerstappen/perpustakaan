@extends('reports.pdf.layout')

@section('title', 'Laporan Anggota')
@section('subtitle', 'Laporan Data Anggota')

@section('content')
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Email Akun</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($members as $i => $member)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $member->nis }}</td>
                    <td>{{ $member->nama }}</td>
                    <td>{{ $member->kelas }}</td>
                    <td>{{ $member->jurusan ?? '-' }}</td>
                    <td>{{ $member->user->email ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
