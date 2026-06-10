@php
    $badgeClass = match (true) {
        ($employee->position?->level ?? 99) <= 1 => 'org-badge-dir',
        ($employee->position?->level ?? 99) === 2 => 'org-badge-mgr',
        ($employee->position?->level ?? 99) === 3 => 'org-badge-spv',
        default => 'org-badge-stf',
    };
@endphp

<a href="{{ route('employees.show', $employee) }}" class="org-employee-branch text-decoration-none">
    <span class="org-employee-branch__avatar"><i class="bx bx-user"></i></span>
    <span class="org-employee-branch__name">{{ $employee->name }}</span>
    @if ($employee->position)
        <span class="org-position-badge {{ $badgeClass }}">{{ strtoupper($employee->position->name) }}</span>
    @endif
</a>
