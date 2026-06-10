@props(['action', 'placeholder' => 'Cari...'])

<form method="GET" action="{{ $action }}" class="row g-2 align-items-center mb-3">
    <div class="col-md-6 col-lg-4">
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="search" name="search" class="form-control" placeholder="{{ $placeholder }}"
                   value="{{ $search ?? request('search') }}">
        </div>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-success">Cari</button>
        @if (request('search'))
            <a href="{{ $action }}" class="btn btn-outline-secondary">Reset</a>
        @endif
    </div>
</form>
