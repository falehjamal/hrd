@php
    $badgeClass = match (true) {
        ($employee->position?->level ?? 99) <= 1 => 'org-badge-dir',
        ($employee->position?->level ?? 99) === 2 => 'org-badge-mgr',
        ($employee->position?->level ?? 99) === 3 => 'org-badge-spv',
        default => 'org-badge-stf',
    };
@endphp

<a href="{{ route('employees.show', $employee) }}" class="org-root-card text-decoration-none">
    <span class="org-root-card__icon"><i class="bx bx-user"></i></span>
    <span class="org-root-card__title">{{ $employee->name }}</span>
    <span class="org-root-card__subtitle">{{ $employee->employee_code }}</span>
    @if ($employee->position)
        <span class="org-position-badge {{ $badgeClass }} mt-2">{{ strtoupper($employee->position->name) }}</span>
    @endif
</a>
