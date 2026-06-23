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
            <li class="nav-item navbar-dropdown dropdown-user dropdown ms-1">
                <a class="nav-link dropdown-toggle navbar-user-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="navbar-user-info d-none d-md-flex">
                        <div class="navbar-user-text">
                            <span class="navbar-user-name">{{ auth('platform')->user()->name }}</span>
                            <span class="navbar-user-role">Platform Admin</span>
                        </div>
                    </div>
                    <div class="avatar avatar-online">
                        <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr(auth('platform')->user()->name, 0, 1)) }}</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="{{ route('platform.logout') }}">
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
