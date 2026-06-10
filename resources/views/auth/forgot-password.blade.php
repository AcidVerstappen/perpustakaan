@extends('layouts.auth')

@section('title', 'Lupa Password')
@section('subtitle', 'Reset password via email')

@section('content')
    <p class="text-muted small">
        Masukkan email Anda. Kami akan mengirimkan link untuk reset password.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">Kirim Link Reset</button>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary">Kembali ke Login</a>
        </div>
    </form>
@endsection
