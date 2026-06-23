<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    @include('partials.navbar-search')

    <div class="navbar-nav-right d-flex align-items-center ms-auto" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center gap-1">
            @include('partials.theme-toggle')
            <li class="nav-item">
                <a class="nav-link navbar-icon-btn" href="javascript:void(0);" aria-label="Notifikasi">
                    <i class="bx bx-bell"></i>
                    <span class="navbar-notification-dot"></span>
                </a>
            </li>
            <li class="nav-item d-none d-sm-block">
                <a class="nav-link navbar-icon-btn" href="{{ auth()->user()->isHrUser() ? route('settings.edit') : route('profile.edit') }}" aria-label="Pengaturan">
                    <i class="bx bx-cog"></i>
                </a>
            </li>
            <li class="nav-item navbar-dropdown dropdown-user dropdown ms-1">
                <a class="nav-link dropdown-toggle navbar-user-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="navbar-user-info d-none d-md-flex">
                        <div class="navbar-user-text">
                            <span class="navbar-user-name">{{ auth()->user()->name }}</span>
                            <span class="navbar-user-role">{{ auth()->user()->isHrUser() ? 'HR Admin' : 'Karyawan' }}</span>
                        </div>
                    </div>
                    <div class="avatar avatar-online">
                        <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Keluar</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
