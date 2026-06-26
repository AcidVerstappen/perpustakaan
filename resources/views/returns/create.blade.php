@extends('layouts.admin')

@section('title', 'Proses Pengembalian')
@section('page-title', 'Proses Pengembalian')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('returns.index') }}">Pengembalian</a></li>
    <li class="breadcrumb-item active">Proses</li>
@endsection

@section('content')
    @if (! $borrowing->canBeReturned())
        <div class="alert alert-danger">
            Peminjaman belum disetujui atau sudah lunas dikembalikan.
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $borrowing->kode_pinjam }}</h5>
                    <span class="badge {{ $borrowing->statusBadgeClass() }}">{{ $borrowing->statusLabel() }}</span>
                </div>
                <div class="card-body">
                    <p><strong>Anggota:</strong> {{ $borrowing->member->nama }} ({{ $borrowing->member->nis }})</p>
                    <p><strong>Jatuh Tempo:</strong> {{ $borrowing->tanggal_jatuh_tempo->format('d F Y') }}</p>

                    @if ($borrowing->hasPartialReturn())
                        <div class="alert alert-info py-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Sudah dikembalikan <strong>{{ $borrowing->totalDikembalikan() }}</strong> dari
                            <strong>{{ $borrowing->totalDipinjam() }}</strong> eksemplar.
                            <strong>Sisa belum dikembalikan: {{ $borrowing->totalSisa() }} eksemplar.</strong>
                        </div>
                    @endif

                    @if ($borrowing->isOverdue())
                        <div class="alert alert-danger py-2">
                            Terlambat {{ $borrowing->tanggal_jatuh_tempo->diffInDays(now()) }} hari.
                            Denda dihitung per eksemplar yang dikembalikan hari ini.
                        </div>
                    @endif

                    <form action="{{ route('returns.store', $borrowing) }}" method="POST">
                        @csrf
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Buku</th>
                                        <th class="text-center" width="80">Pinjam</th>
                                        <th class="text-center" width="80">Sudah Kembali</th>
                                        <th class="text-center" width="80">Sisa</th>
                                        <th class="text-center" width="120">Kembali Sekarang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($borrowing->details as $detail)
                                        @php $sisa = $detail->qtySisa(); @endphp
                                        @if ($sisa > 0)
                                            <tr>
                                                <td>
                                                    <strong>{{ $detail->book->judul }}</strong><br>
                                                    <small class="text-muted">{{ $detail->book->kode_buku }}</small>
                                                </td>
                                                <td class="text-center">{{ $detail->qty }}</td>
                                                <td class="text-center">{{ $detail->qty_dikembalikan }}</td>
                                                <td class="text-center">
                                                    <span class="badge text-bg-warning">{{ $sisa }}</span>
                                                </td>
                                                <td>
                                                    <input type="number"
                                                           name="items[{{ $detail->id }}][qty]"
                                                           class="form-control form-control-sm text-center @error('items.'.$detail->id.'.qty') is-invalid @enderror"
                                                           value="{{ old('items.'.$detail->id.'.qty', $sisa) }}"
                                                           min="0"
                                                           max="{{ $sisa }}">
                                                    @error('items.'.$detail->id.'.qty')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @error('items')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        <div class="mb-3">
                            <label for="tanggal_kembali" class="form-label">Tanggal Kembali</label>
                            <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control"
                                   value="{{ old('tanggal_kembali', now()->format('Y-m-d')) }}"
                                   min="{{ $borrowing->tanggal_pinjam->format('Y-m-d') }}">
                        </div>

                        <div class="alert alert-success small mb-3">
                            <i class="bi bi-arrow-up-circle me-1"></i>
                            Bisa dikembalikan <strong>sebagian</strong>. Stok bertambah sesuai jumlah di atas.
                            Jika masih ada sisa, peminjaman tetap aktif sampai semua buku kembali.
                        </div>

                        @if ($isPetugas ?? false)
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-check me-1"></i> Validasi Kondisi Buku</h6>
                                <div class="mb-3">
                                    <label for="kondisi_buku" class="form-label fw-medium">Kondisi Buku <span class="text-danger">*</span></label>
                                    <select name="kondisi_buku" id="kondisi_buku" class="form-select @error('kondisi_buku') is-invalid @enderror" required>
                                        <option value="">-- Pilih Kondisi --</option>
                                        <option value="baik" @selected(old('kondisi_buku') === 'baik')>Baik</option>
                                        <option value="rusak" @selected(old('kondisi_buku') === 'rusak')>Rusak</option>
                                        <option value="hilang" @selected(old('kondisi_buku') === 'hilang')>Hilang</option>
                                    </select>
                                    @error('kondisi_buku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-0">
                                    <label for="catatan_kondisi" class="form-label fw-medium">Catatan Kondisi</label>
                                    <textarea name="catatan_kondisi" id="catatan_kondisi" rows="3"
                                              class="form-control @error('catatan_kondisi') is-invalid @enderror"
                                              placeholder="Catat kerusakan atau kehilangan buku (opsional)...">{{ old('catatan_kondisi') }}</textarea>
                                    @error('catatan_kondisi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Proses pengembalian buku?')">
                            <i class="bi bi-check-lg"></i> Konfirmasi Pengembalian
                        </button>
                        <a href="{{ route('returns.index') }}" class="btn btn-outline-secondary w-100 mt-2">Batal</a>
                    </form>
                </div>
            </div>

            @if ($borrowing->returnLogs->isNotEmpty())
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white"><h6 class="mb-0">Riwayat Pengembalian Sebagian</h6></div>
                    <ul class="list-group list-group-flush small">
                        @foreach ($borrowing->returnLogs->sortByDesc('created_at') as $log)
                            <li class="list-group-item">
                                <strong>{{ $log->tanggal_kembali->format('d/m/Y') }}</strong>
                                — {{ $log->total_qty_kembali }} eks. ({{ $log->receiver->name }})<br>
                                <span class="text-muted">{{ $log->ringkasan }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body small">
                    <h6 class="fw-bold">Contoh</h6>
                    <p class="mb-2">Pinjam 5 eksemplar buku A → kembalikan 2 → <strong>sisa 3</strong> masih dipinjam. Bisa kembalikan lagi nanti sampai lunas.</p>
                    <h6 class="fw-bold mt-3">Alur stok</h6>
                    <ol class="mb-0 ps-3">
                        <li class="mb-2"><strong>Disetujui</strong> — stok berkurang</li>
                        <li class="mb-2"><strong>Kembali sebagian</strong> — stok naik sebagian, status tetap dipinjam</li>
                        <li><strong>Semua kembali</strong> — status selesai</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection
