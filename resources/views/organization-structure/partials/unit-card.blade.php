@php
    $primaryEmployee = $unit->employees
        ->sortBy(fn ($employee) => $employee->position?->level ?? 99)
        ->first();
    $badgeClass = match (true) {
        ($primaryEmployee?->position?->level ?? 99) <= 1 => 'org-badge-dir',
        ($primaryEmployee?->position?->level ?? 99) === 2 => 'org-badge-mgr',
        ($primaryEmployee?->position?->level ?? 99) === 3 => 'org-badge-spv',
        default => 'org-badge-stf',
    };
@endphp

<div class="org-unit-card">
    <div class="org-unit-card__header">
        <span class="org-unit-card__icon"><i class="bx bx-buildings"></i></span>
        <span class="org-unit-card__title">{{ $unit->name }}</span>
    </div>

    @if ($primaryEmployee)
        <a href="{{ route('employees.show', $primaryEmployee) }}" class="org-unit-card__body text-decoration-none">
            <span class="org-unit-card__avatar">
                <i class="bx bx-user"></i>
            </span>
            <span class="org-unit-card__name">{{ $primaryEmployee->name }}</span>
            <span class="org-unit-card__code">{{ $primaryEmployee->employee_code }}</span>
            @if ($primaryEmployee->position)
                <span class="org-position-badge {{ $badgeClass }}">{{ strtoupper($primaryEmployee->position->name) }}</span>
            @endif
        </a>
    @else
        <div class="org-unit-card__body org-unit-card__body--empty">
            <span class="text-muted small">Belum ada karyawan</span>
        </div>
    @endif
</div>
