<p class="text-muted small mb-3">Gunakan password yang kuat dan unik.</p>

<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
        <label for="update_password_current_password" class="form-label">Password Saat Ini</label>
        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
               id="update_password_current_password" name="current_password" autocomplete="current-password">
        @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password" class="form-label">Password Baru</label>
        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror"
               id="update_password_password" name="password" autocomplete="new-password">
        @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="update_password_password_confirmation" class="form-label">Konfirmasi Password</label>
        <input type="password" class="form-control" id="update_password_password_confirmation"
               name="password_confirmation" autocomplete="new-password">
    </div>

    <button type="submit" class="btn btn-success">Simpan Password</button>
    @if (session('status') === 'password-updated')
        <span class="text-success small ms-2">Tersimpan.</span>
    @endif
</form>
