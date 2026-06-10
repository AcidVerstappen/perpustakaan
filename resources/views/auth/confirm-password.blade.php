@extends('layouts.auth')

@section('title', 'Konfirmasi Password')
@section('subtitle', 'Area aman — konfirmasi password Anda')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror"
                   id="password" name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-success">Konfirmasi</button>
        </div>
    </form>
@endsection
