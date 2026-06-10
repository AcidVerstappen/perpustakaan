<div class="row">
    <div class="col-md-4 mb-3">
        <label for="nis" class="form-label">NIS <span class="text-danger">*</span></label>
        <input type="text" name="nis" id="nis"
               class="form-control @error('nis') is-invalid @enderror"
               value="{{ old('nis', $member->nis ?? '') }}" required>
        @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8 mb-3">
        <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
        <input type="text" name="nama" id="nama"
               class="form-control @error('nama') is-invalid @enderror"
               value="{{ old('nama', $member->nama ?? '') }}" required>
        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
        <input type="text" name="kelas" id="kelas"
               class="form-control @error('kelas') is-invalid @enderror"
               value="{{ old('kelas', $member->kelas ?? '') }}" required>
        @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="jurusan" class="form-label">Jurusan</label>
        <input type="text" name="jurusan" id="jurusan"
               class="form-control @error('jurusan') is-invalid @enderror"
               value="{{ old('jurusan', $member->jurusan ?? '') }}">
        @error('jurusan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
        <select name="jenis_kelamin" id="jenis_kelamin" class="form-select @error('jenis_kelamin') is-invalid @enderror" required>
            <option value="">-- Pilih --</option>
            <option value="L" @selected(old('jenis_kelamin', $member->jenis_kelamin ?? '') === 'L')>Laki-laki</option>
            <option value="P" @selected(old('jenis_kelamin', $member->jenis_kelamin ?? '') === 'P')>Perempuan</option>
        </select>
        @error('jenis_kelamin')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <label for="alamat" class="form-label">Alamat</label>
    <textarea name="alamat" id="alamat" rows="2"
              class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $member->alamat ?? '') }}</textarea>
    @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="no_hp" class="form-label">No. HP</label>
        <input type="text" name="no_hp" id="no_hp"
               class="form-control @error('no_hp') is-invalid @enderror"
               value="{{ old('no_hp', $member->no_hp ?? '') }}">
        @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="foto" class="form-label">Foto</label>
        <input type="file" name="foto" id="foto" accept="image/*"
               class="form-control @error('foto') is-invalid @enderror">
        @error('foto')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<hr>
<h6 class="mb-3">Akun Login Siswa (opsional)</h6>

@php
    $hasAccount = ! empty($member?->user_id);
    $showAccountFields = old('buat_akun') || $hasAccount;
@endphp

@if (! $hasAccount)
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="buat_akun" name="buat_akun" value="1"
               @checked(old('buat_akun'))>
        <label class="form-check-label" for="buat_akun">Buat akun login untuk siswa ini</label>
    </div>
@else
    <p class="small text-muted">Akun terhubung: <strong>{{ $member->user->email }}</strong></p>
@endif

<div id="accountFields" class="{{ $showAccountFields ? '' : 'd-none' }}">
    <div class="mb-3">
        <label for="email" class="form-label">Email @if (! $hasAccount)<span class="text-danger account-required">*</span>@endif</label>
        <input type="email" name="email" id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $member->user->email ?? '') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Password @if (! $hasAccount)<span class="text-danger account-required">*</span>@endif</label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        </div>
    </div>
    @if ($hasAccount)
        <p class="small text-muted mb-0">Kosongkan password jika tidak ingin mengubah.</p>
    @endif
</div>

@push('scripts')
<script>
    const buatAkun = document.getElementById('buat_akun');
    const accountFields = document.getElementById('accountFields');
    buatAkun?.addEventListener('change', function () {
        accountFields.classList.toggle('d-none', !this.checked);
    });
</script>
@endpush
