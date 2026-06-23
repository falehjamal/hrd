<div class="sidebar-footer">
    <a href="{{ auth()->user()->isHrUser() ? route('settings.edit') : route('profile.edit') }}" class="btn btn-primary w-100 sidebar-support-btn">
        <i class="bx bx-support me-2"></i> Pusat Bantuan
    </a>
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-link sidebar-logout-btn w-100">
            <i class="bx bx-log-out me-2"></i> Keluar
        </button>
    </form>
</div>
