@extends('layouts.admin')

@section('title', 'Tambah Peminjaman')
@section('page-title', 'Tambah Peminjaman')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('borrowings.index') }}">Peminjaman</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-1"></i>
        Peminjaman dibuat dengan status <strong>Diajukan</strong>. Setujui peminjaman untuk mengurangi stok buku.
        Lama pinjam default: <strong>{{ config('perpustakaan.hari_peminjaman', 7) }} hari</strong>.
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('borrowings.store') }}" method="POST" id="borrowingForm">
                @csrf
                <div class="mb-3">
                    <label for="member_id" class="form-label">Anggota <span class="text-danger">*</span></label>
                    <select name="member_id" id="member_id" class="form-select select-search @error('member_id') is-invalid @enderror"
                            data-placeholder="Cari anggota (NIS / nama)..." required>
                        <option value="">-- Pilih Anggota --</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" @selected(old('member_id') == $member->id)>
                                {{ $member->nis }} - {{ $member->nama }} ({{ $member->kelas }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <x-select-search-hint />
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Daftar Buku</h6>
                    <button type="button" class="btn btn-sm btn-outline-success" id="addBookRow">
                        <i class="bi bi-plus"></i> Tambah Baris
                    </button>
                </div>
                @error('books')<div class="text-danger small mb-2">{{ $message }}</div>@enderror

                <div id="bookRows">
                    <div class="row g-2 mb-2 book-row">
                        <div class="col-md-8">
                            <select name="books[0][book_id]" class="form-select select-search"
                                    data-placeholder="Cari buku (kode / judul)..." required>
                                <option value="">-- Pilih Buku --</option>
                                @foreach ($books as $book)
                                    <option value="{{ $book->id }}">
                                        {{ $book->kode_buku }} - {{ $book->judul }} (stok: {{ $book->stok_tersedia }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="books[0][qty]" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100 remove-row" disabled><i class="bi bi-x"></i></button>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">Simpan Peminjaman</button>
                    <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const bookOptions = @json($books->map(fn($b) => [
        'id' => $b->id,
        'label' => $b->kode_buku . " - " . $b->judul . " (stok: " . $b->stok_tersedia . ")",
        'stok' => $b->stok_tersedia
    ]));
    let rowIndex = 1;

    function syncBookSelections() {
        const selectedValues = [];
        const rows = document.querySelectorAll('.book-row');
        
        rows.forEach(row => {
            const select = row.querySelector('select.select-search');
            if (select && select.value) {
                selectedValues.push({
                    select: select,
                    value: select.value
                });
            }
        });

        rows.forEach(row => {
            const select = row.querySelector('select.select-search');
            const qtyInput = row.querySelector('input[type="number"]');
            
            if (!select || !qtyInput) return;

            const selectedVal = select.value;

            if (selectedVal) {
                const book = bookOptions.find(b => b.id == selectedVal);
                if (book) {
                    qtyInput.max = book.stok;
                    if (parseInt(qtyInput.value) > book.stok) {
                        qtyInput.value = book.stok;
                    }
                }
            } else {
                qtyInput.removeAttribute('max');
            }

            const ts = select.tomselect;
            if (ts) {
                bookOptions.forEach(book => {
                    const shouldDisable = selectedValues.some(item => item.value == book.id && item.select !== select);
                    const option = ts.options[book.id];
                    if (option) {
                        option.disabled = shouldDisable;
                        ts.updateOption(book.id, option);
                    }
                });
                ts.refreshOptions(false);
            }
        });
    }

    document.getElementById('addBookRow').addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 book-row';
        let options = '<option value="">-- Pilih Buku --</option>';
        bookOptions.forEach(b => { options += `<option value="${b.id}">${b.label}</option>`; });
        row.innerHTML = `
            <div class="col-md-8">
                <select name="books[${rowIndex}][book_id]" class="form-select select-search"
                        data-placeholder="Cari buku (kode / judul)..." required>${options}</select>
            </div>
            <div class="col-md-3"><input type="number" name="books[${rowIndex}][qty]" class="form-control" value="1" min="1" required></div>
            <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button></div>`;
        document.getElementById('bookRows').appendChild(row);
        window.initSelectSearch(row);
        rowIndex++;
        updateRemoveButtons();
        syncBookSelections();
    });

    document.getElementById('bookRows').addEventListener('click', e => {
        if (e.target.closest('.remove-row')) {
            const row = e.target.closest('.book-row');
            const select = row.querySelector('select');
            if (select) window.destroySelectSearch(select);
            row.remove();
            updateRemoveButtons();
            syncBookSelections();
        }
    });

    document.getElementById('bookRows').addEventListener('change', e => {
        if (e.target.tagName === 'SELECT') {
            syncBookSelections();
        }
    });

    document.getElementById('bookRows').addEventListener('input', e => {
        if (e.target.type === 'number') {
            const qtyInput = e.target;
            const max = parseInt(qtyInput.max);
            if (max && parseInt(qtyInput.value) > max) {
                qtyInput.value = max;
            }
        }
    });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.book-row');
        rows.forEach((row) => {
            row.querySelector('.remove-row').disabled = rows.length <= 1;
        });
    }

    setTimeout(() => {
        syncBookSelections();
    }, 100);
</script>
@endpush
