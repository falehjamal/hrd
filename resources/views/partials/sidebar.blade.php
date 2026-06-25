<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme hrd-sidebar">
    @include('partials.sidebar-brand', [
        'href' => route('dashboard'),
        'title' => tenant_sidebar_title(),
        'subtitle' => 'Enterprise Admin',
    ])

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        @if (auth()->user()->isHrUser())
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Master Data</span></li>

        <li class="menu-item {{ request()->routeIs('organization-structure.*') ? 'active' : '' }}">
            <a href="{{ route('organization-structure.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-sitemap"></i>
                <div>Struktur Organisasi</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('organizational-units.*') ? 'active' : '' }}">
            <a href="{{ route('organizational-units.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-buildings"></i>
                <div>Unit Organisasi</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('branches.*') ? 'active' : '' }}">
            <a href="{{ route('branches.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-git-branch"></i>
                <div>Data Cabang</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('positions.*') ? 'active' : '' }}">
            <a href="{{ route('positions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-briefcase"></i>
                <div>Data Jabatan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
            <a href="{{ route('shifts.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-time"></i>
                <div>Data Shift</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('employees.*', 'employees.salaries.*', 'employees.weekly-shifts.*', 'employees.deductions.*', 'employees.employee-loans.*', 'employees.leave-balances.*') ? 'active' : '' }}">
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

        <li class="menu-item {{ request()->routeIs('leave-types.*') ? 'active' : '' }}">
            <a href="{{ route('leave-types.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div>Jenis Cuti</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('deduction-types.*') ? 'active' : '' }}">
            <a href="{{ route('deduction-types.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-minus-circle"></i>
                <div>Jenis Pemotongan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('deductions.*', 'employees.deductions.*') ? 'active' : '' }}">
            <a href="{{ route('deductions.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div>Master Pemotongan</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('employee-loans.*', 'employees.employee-loans.*') ? 'active' : '' }}">
            <a href="{{ route('employee-loans.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-credit-card"></i>
                <div>Piutang Karyawan</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Kompensasi</span></li>

        <li class="menu-item {{ request()->routeIs('payroll-periods.*') ? 'active' : '' }}">
            <a href="{{ route('payroll-periods.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div>Proses Gaji</div>
            </a>
        </li>
        @endif

        <li class="menu-header small text-uppercase"><span class="menu-header-text">Operasional</span></li>

        @if (auth()->user()->hasActiveEmployeeLink())
        <li class="menu-item {{ request()->routeIs('attendances.check-in*') ? 'active' : '' }}">
            <a href="{{ route('attendances.check-in') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-current-location"></i>
                <div>Absen Saya</div>
            </a>
        </li>

        <li class="menu-item {{ request()->routeIs('payslips.*') ? 'active' : '' }}">
            <a href="{{ route('payslips.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div>Slip Gaji</div>
            </a>
        </li>
        @endif

        @if (auth()->user()->isHrUser())
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

        <li class="menu-item {{ request()->routeIs('leave-requests.*') ? 'active' : '' }}">
            <a href="{{ route('leave-requests.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-plane-alt"></i>
                <div>Cuti</div>
            </a>
        </li>

        @if (auth()->user()->isHrUser())
        <li class="menu-header small text-uppercase"><span class="menu-header-text">Sistem</span></li>

        <li class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <a href="{{ route('settings.edit') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Pengaturan</div>
            </a>
        </li>
        @endif
    </ul>

    @include('partials.sidebar-footer')
</aside>
