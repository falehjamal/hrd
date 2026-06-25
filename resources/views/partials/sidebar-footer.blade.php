<div class="sidebar-footer">
    @if (auth()->user()->isHrUser())
        <a href="{{ route('settings.edit') }}" class="btn btn-primary w-100 sidebar-support-btn">
            <i class="bx bx-cog me-2"></i> Pengaturan
        </a>
    @else
        <a href="{{ route('profile.edit') }}" class="btn btn-primary w-100 sidebar-support-btn">
            <i class="bx bx-user me-2"></i> Profil Saya
        </a>
    @endif
    <form method="POST" action="{{ route('logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-link sidebar-logout-btn w-100">
            <i class="bx bx-log-out me-2"></i> Keluar
        </button>
    </form>
</div>
