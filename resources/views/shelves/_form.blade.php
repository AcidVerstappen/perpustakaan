<div class="row">
    <div class="col-md-4 mb-3">
        <label for="kode_rak" class="form-label">Kode Rak <span class="text-danger">*</span></label>
        <input type="text" name="kode_rak" id="kode_rak"
               class="form-control @error('kode_rak') is-invalid @enderror"
               value="{{ old('kode_rak', $shelf->kode_rak ?? '') }}" required>
        @error('kode_rak')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8 mb-3">
        <label for="nama_rak" class="form-label">Nama Rak <span class="text-danger">*</span></label>
        <input type="text" name="nama_rak" id="nama_rak"
               class="form-control @error('nama_rak') is-invalid @enderror"
               value="{{ old('nama_rak', $shelf->nama_rak ?? '') }}" required>
        @error('nama_rak')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="mb-3">
    <label for="lokasi" class="form-label">Lokasi</label>
    <input type="text" name="lokasi" id="lokasi"
           class="form-control @error('lokasi') is-invalid @enderror"
           value="{{ old('lokasi', $shelf->lokasi ?? '') }}">
    @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
