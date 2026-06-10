@php
    $isAdmin = auth()->user()->hasAnyRole(['Super Admin', 'Admin Perpustakaan']);

    $menuItems = [
        ['route' => 'dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'pattern' => 'dashboard'],
        ['route' => 'books.index', 'icon' => 'bi-book', 'label' => $isAdmin ? 'Buku' : 'Katalog Buku', 'pattern' => 'books.*'],
        ['route' => 'borrowings.index', 'icon' => 'bi-arrow-left-right', 'label' => $isAdmin ? 'Peminjaman' : 'Riwayat Peminjaman', 'pattern' => 'borrowings.*'],
        ['route' => 'fines.index', 'icon' => 'bi-cash-coin', 'label' => 'Denda', 'pattern' => 'fines.*'],
    ];

    if ($isAdmin) {
        $adminMenus = [
            ['route' => 'categories.index', 'icon' => 'bi-tags', 'label' => 'Kategori', 'pattern' => 'categories.*'],
            ['route' => 'shelves.index', 'icon' => 'bi-grid-3x3-gap', 'label' => 'Rak', 'pattern' => 'shelves.*'],
            ['route' => 'members.index', 'icon' => 'bi-people', 'label' => 'Anggota', 'pattern' => 'members.*'],
            ['route' => 'returns.index', 'icon' => 'bi-box-arrow-in-left', 'label' => 'Pengembalian', 'pattern' => 'returns.*'],
            ['route' => 'reports.index', 'icon' => 'bi-file-earmark-bar-graph', 'label' => 'Laporan', 'pattern' => 'reports.*'],
        ];
        $menuItems = array_merge(
            array_slice($menuItems, 0, 2),
            $adminMenus,
            array_slice($menuItems, 2)
        );
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
                <div class="text-white text-truncate small fw-semibold">{{ auth()->user()->name }}</div>
                <div class="text-success small">
                    <i class="bi bi-circle-fill" style="font-size: 0.4rem;"></i> Online
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
