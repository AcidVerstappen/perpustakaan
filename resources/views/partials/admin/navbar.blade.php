<header class="admin-navbar">
    <button type="button" class="btn btn-link text-white p-0 me-3 d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list fs-3"></i>
    </button>
    <span class="fw-semibold d-none d-sm-inline">{{ config('app.name') }}</span>
    <div class="ms-auto dropdown">
        <button class="btn btn-link text-white text-decoration-none dropdown-toggle d-flex align-items-center gap-2"
                type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="rounded-circle bg-white bg-opacity-25 d-inline-flex align-items-center justify-content-center"
                  style="width: 32px; height: 32px;">
                <i class="bi bi-person-fill"></i>
            </span>
            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <span class="dropdown-item-text small text-muted">
                    {{ auth()->user()->getRoleNames()->first() ?? 'User' }}
                </span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person-gear me-2"></i> Profil
                </a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>
