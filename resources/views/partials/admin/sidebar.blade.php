@php
    $user = auth()->user();
    $isAdmin = $user->isAdminLibrary();
    $isPetugas = $user->isPetugas();

    if ($isAdmin) {
        $menuItems = [
            ['route' => 'dashboard',        'icon' => 'bi-speedometer2',          'label' => 'Dashboard',           'pattern' => 'dashboard'],
            ['route' => 'members.index',    'icon' => 'bi-people',                'label' => 'Data Anggota',        'pattern' => 'members.*'],
            ['route' => 'books.index',      'icon' => 'bi-book',                  'label' => 'Data Buku',           'pattern' => 'books.*'],
            ['route' => 'categories.index', 'icon' => 'bi-tags',                  'label' => 'Data Kategori',       'pattern' => 'categories.*'],
            ['route' => 'shelves.index',    'icon' => 'bi-grid-3x3-gap',          'label' => 'Data Rak',            'pattern' => 'shelves.*'],
            ['route' => 'borrowings.index', 'icon' => 'bi-arrow-left-right',      'label' => 'Data Peminjaman',     'pattern' => 'borrowings.*'],
            ['route' => 'returns.index',    'icon' => 'bi-box-arrow-in-left',     'label' => 'Data Pengembalian',   'pattern' => 'returns.*'],
            ['route' => 'fines.index',      'icon' => 'bi-cash-coin',             'label' => 'Data Denda',          'pattern' => 'fines.*'],
            ['route' => 'reports.index',    'icon' => 'bi-file-earmark-bar-graph','label' => 'Laporan PDF',         'pattern' => 'reports.*'],
        ];
    } elseif ($isPetugas) {
        $menuItems = [
            ['route' => 'dashboard',        'icon' => 'bi-speedometer2',      'label' => 'Dashboard',           'pattern' => 'dashboard'],
            ['route' => 'members.index',    'icon' => 'bi-people',            'label' => 'Data Anggota',        'pattern' => 'members.*'],
            ['route' => 'borrowings.index', 'icon' => 'bi-arrow-left-right',  'label' => 'Peminjaman Buku',     'pattern' => 'borrowings.*'],
            ['route' => 'returns.index',    'icon' => 'bi-box-arrow-in-left', 'label' => 'Pengembalian Buku',   'pattern' => 'returns.*'],
            ['route' => 'fines.index',      'icon' => 'bi-cash-coin',         'label' => 'Data Denda',          'pattern' => 'fines.*'],
        ];
    } else {
        $menuItems = [
            ['route' => 'dashboard',        'icon' => 'bi-speedometer2',      'label' => 'Dashboard',           'pattern' => 'dashboard'],
            ['route' => 'books.index',      'icon' => 'bi-book',              'label' => 'Katalog Buku',        'pattern' => 'books.*'],
            ['route' => 'borrowings.index', 'icon' => 'bi-arrow-left-right',  'label' => 'Riwayat Peminjaman',  'pattern' => 'borrowings.*'],
            ['route' => 'fines.index',      'icon' => 'bi-cash-coin',         'label' => 'Denda',               'pattern' => 'fines.*'],
            ['route' => 'booking.cart',     'icon' => 'bi-cart',              'label' => 'Keranjang Booking',   'pattern' => 'booking.cart'],
        ];
    }
@endphp

<aside class="admin-sidebar" id="adminSidebar">
    <div class="brand d-flex align-items-center gap-2">
        <i class="bi bi-book-half fs-4 text-success"></i>
        <span class="text-truncate">{{ config('app.name') }}</span>
    </div>

    <div class="p-3 border-bottom border-secondary border-opacity-25">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-success d-flex align-items-center justify-content-center text-white"
                 style="width: 40px; height: 40px;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="overflow-hidden">
                <div class="text-white text-truncate small fw-semibold">{{ $user->name }}</div>
                <div class="text-success small">
                    <i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i>
                    {{ $user->getRoleNames()->first() ?? 'User' }}
                </div>
            </div>
        </div>
    </div>

    <nav class="nav flex-column py-2">
        <div class="nav-section">Menu Utama</div>
        @foreach ($menuItems as $item)
            <a href="{{ route($item['route']) }}"
               class="nav-link {{ request()->routeIs($item['pattern']) ? 'active' : '' }}">
                <i class="bi {{ $item['icon'] }}"></i>
                {{ $item['label'] }}
            </a>
        @endforeach

        <div class="nav-section mt-2">Pengaturan</div>
        <a href="{{ route('profile.edit') }}"
           class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i> Profil
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </nav>
</aside>
