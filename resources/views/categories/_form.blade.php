<div class="mb-3">
    <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
    <input type="text" name="nama_kategori" id="nama_kategori"
           class="form-control @error('nama_kategori') is-invalid @enderror"
           value="{{ old('nama_kategori', $category->nama_kategori ?? '') }}" required>
    @error('nama_kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="deskripsi" class="form-label">Deskripsi</label>
    <textarea name="deskripsi" id="deskripsi" rows="3"
              class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $category->deskripsi ?? '') }}</textarea>
    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
