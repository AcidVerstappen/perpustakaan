@extends('layouts.auth')

@section('title', 'Login')
@section('subtitle', 'Masuk ke akun Anda')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror"
                   id="password" name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">Ingat saya</label>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
            </button>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a href="{{ route('password.request') }}" class="small text-decoration-none">Lupa password?</a>
            </div>
        @endif
    </form>

    <div class="mt-4 p-3 bg-light rounded small">
        <strong>Akun development (lokal):</strong>
        <ul class="mb-0 mt-1">
            <li>admin@perpus.test / password</li>
            <li>petugas@perpus.test / password</li>
            <li>siswa@perpus.test / password</li>
        </ul>
    </div>
@endsection
