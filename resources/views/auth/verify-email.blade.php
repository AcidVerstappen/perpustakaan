@extends('layouts.auth')

@section('title', 'Verifikasi Email')
@section('subtitle', 'Konfirmasi alamat email Anda')

@section('content')
    <p class="text-muted small mb-3">
        Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi email dengan mengklik link yang kami kirim.
        Jika belum menerima email, kami dapat mengirim ulang.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success">
            Link verifikasi baru telah dikirim ke email Anda.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success w-100 mb-2">Kirim Ulang Email Verifikasi</button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary w-100">Keluar</button>
    </form>
@endsection
