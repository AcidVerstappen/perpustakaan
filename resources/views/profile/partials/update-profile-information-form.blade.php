<p class="text-muted small mb-3">Perbarui nama dan alamat email akun Anda.</p>

<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="mb-3">
        <label for="name" class="form-label">Nama</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror"
               id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror"
               id="email" name="email" value="{{ old('email', $user->email) }}" required>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="small text-muted mt-2 mb-0">
                Email belum diverifikasi.
                <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">
                    Kirim ulang email verifikasi
                </button>
            </p>
            @if (session('status') === 'verification-link-sent')
                <p class="small text-success mt-1 mb-0">Link verifikasi baru telah dikirim.</p>
            @endif
        @endif
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
    @if (session('status') === 'profile-updated')
        <span class="text-success small ms-2">Tersimpan.</span>
    @endif
</form>
