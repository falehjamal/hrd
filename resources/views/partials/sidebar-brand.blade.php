<div class="app-brand demo">
    <a href="{{ $href }}" class="app-brand-link">
        <span class="app-brand-logo">
            <i class="bx bx-buildings"></i>
        </span>
        <span class="app-brand-text">
            <span class="app-brand-name sidebar-brand-uppercase">{{ $title }}</span>
            @if (!empty($subtitle))
                <span class="app-brand-subtitle">{{ $subtitle }}</span>
            @endif
        </span>
    </a>
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto"
       aria-label="Perkecil menu" title="Perkecil menu">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
</div>
