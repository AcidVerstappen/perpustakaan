<div class="row">
    <div class="col-md-4 mb-3">
        <label for="kode_buku" class="form-label">Kode Buku <span class="text-danger">*</span></label>
        <input type="text" name="kode_buku" id="kode_buku"
               class="form-control @error('kode_buku') is-invalid @enderror"
               value="{{ old('kode_buku', $book->kode_buku ?? '') }}" required>
        @error('kode_buku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="isbn" class="form-label">ISBN</label>
        <input type="text" name="isbn" id="isbn"
               class="form-control @error('isbn') is-invalid @enderror"
               value="{{ old('isbn', $book->isbn ?? '') }}">
        @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
        <input type="number" name="tahun_terbit" id="tahun_terbit"
               class="form-control @error('tahun_terbit') is-invalid @enderror"
               value="{{ old('tahun_terbit', $book->tahun_terbit ?? '') }}" min="1900" max="{{ date('Y') }}">
        @error('tahun_terbit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <label for="judul" class="form-label">Judul <span class="text-danger">*</span></label>
    <input type="text" name="judul" id="judul"
           class="form-control @error('judul') is-invalid @enderror"
           value="{{ old('judul', $book->judul ?? '') }}" required>
    @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
        <select name="category_id" id="category_id" class="form-select select-search @error('category_id') is-invalid @enderror"
                data-placeholder="Cari kategori..." required>
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id ?? '') == $category->id)>
                    {{ $category->nama_kategori }}
                </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <x-select-search-hint />
    </div>
    <div class="col-md-6 mb-3">
        <label for="shelf_id" class="form-label">Rak <span class="text-danger">*</span></label>
        <select name="shelf_id" id="shelf_id" class="form-select select-search @error('shelf_id') is-invalid @enderror"
                data-placeholder="Cari rak..." required>
            <option value="">-- Pilih Rak --</option>
            @foreach ($shelves as $shelf)
                <option value="{{ $shelf->id }}" @selected(old('shelf_id', $book->shelf_id ?? '') == $shelf->id)>
                    {{ $shelf->kode_rak }} - {{ $shelf->nama_rak }}
                </option>
            @endforeach
        </select>
        @error('shelf_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <x-select-search-hint />
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="penulis" class="form-label">Penulis <span class="text-danger">*</span></label>
        <input type="text" name="penulis" id="penulis"
               class="form-control @error('penulis') is-invalid @enderror"
               value="{{ old('penulis', $book->penulis ?? '') }}" required>
        @error('penulis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="penerbit" class="form-label">Penerbit</label>
        <input type="text" name="penerbit" id="penerbit"
               class="form-control @error('penerbit') is-invalid @enderror"
               value="{{ old('penerbit', $book->penerbit ?? '') }}">
        @error('penerbit')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="jumlah_buku" class="form-label">Jumlah Buku <span class="text-danger">*</span></label>
        <input type="number" name="jumlah_buku" id="jumlah_buku" min="1"
               class="form-control @error('jumlah_buku') is-invalid @enderror"
               value="{{ old('jumlah_buku', $book->jumlah_buku ?? 1) }}" required>
        @error('jumlah_buku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="stok_tersedia" class="form-label">Stok Tersedia <span class="text-danger">*</span></label>
        <input type="number" name="stok_tersedia" id="stok_tersedia" min="0"
               class="form-control @error('stok_tersedia') is-invalid @enderror"
               value="{{ old('stok_tersedia', $book->stok_tersedia ?? 1) }}" required>
        @error('stok_tersedia')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="cover" class="form-label">Cover Buku</label>
        <input type="file" name="cover" id="cover" accept="image/*"
               class="form-control @error('cover') is-invalid @enderror">
        @error('cover')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if (! empty($book?->cover))
            <img src="{{ $book->cover_url }}" alt="Cover" class="img-thumbnail mt-2" style="max-height: 120px;">
        @endif
    </div>
</div>

<div class="mb-3">
    <label for="deskripsi" class="form-label">Deskripsi</label>
    <textarea name="deskripsi" id="deskripsi" rows="4"
              class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $book->deskripsi ?? '') }}</textarea>
    @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@push('scripts')
<script>
    document.getElementById('jumlah_buku')?.addEventListener('change', function () {
        const stok = document.getElementById('stok_tersedia');
        if (stok && !stok.dataset.touched) {
            stok.value = this.value;
        }
    });
    document.getElementById('stok_tersedia')?.addEventListener('input', function () {
        this.dataset.touched = '1';
    });
</script>
@endpush
