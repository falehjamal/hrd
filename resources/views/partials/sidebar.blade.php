<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder sidebar-brand-uppercase">{{ tenant_sidebar_title() }}</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        @if (!auth()->user()->employee)
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Master Data</span></li>

        <li class="menu-item {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
            <a href="{{ route('shifts.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-time"></i>
                <div>Data Shift</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('employees.*', 'employees.salaries.*', 'employees.weekly-shifts.*') ? 'active' : '' }}">
            <a href="{{ route('employees.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Data Karyawan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('salaries.*') ? 'active' : '' }}">
            <a href="{{ route('salaries.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div>Master Gaji</div>
            </a>
        </li>
        @endif

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Operasional</span></li>

        @if (auth()->user()->employee)
        <li class="menu-item {{ request()->routeIs('attendances.check-in*') ? 'active' : '' }}">
            <a href="{{ route('attendances.check-in') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-current-location"></i>
                <div>Absen Saya</div>
            </a>
        </li>
        @endif

        @if (!auth()->user()->employee)
        <li class="menu-item {{ request()->routeIs('attendances.*') && !request()->routeIs('attendances.check-in*') ? 'active' : '' }}">
            <a href="{{ route('attendances.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar-check"></i>
                <div>Absensi</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('work-locations.*') ? 'active' : '' }}">
            <a href="{{ route('work-locations.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-map"></i>
                <div>Lokasi Kerja</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('shift-overrides.*') ? 'active' : '' }}">
            <a href="{{ route('shift-overrides.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar-event"></i>
                <div>Jadwal Shift</div>
            </a>
        </li>
        @endif

        <li class="menu-item {{ request()->routeIs('overtime-requests.*') ? 'active' : '' }}">
            <a href="{{ route('overtime-requests.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-time-five"></i>
                <div>Lembur</div>
            </a>
        </li>

        @if (!auth()->user()->employee)
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Sistem</span></li>

        <li class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <a href="{{ route('settings.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Pengaturan</div>
            </a>
        </li>
        @endif
    </ul>
</aside>
