@php
    $hasSubordinates = $employee->subordinates->isNotEmpty();
@endphp

<div class="org-chart-h__row {{ $hasSubordinates ? 'org-chart-h__row--branch' : '' }}">
    <div class="org-chart-h__unit">
        @include('organization-structure.partials.employee-branch-card', ['employee' => $employee])
    </div>

    @if ($hasSubordinates)
        <div class="org-chart-h__branch-spine" aria-hidden="true"></div>
        <div class="org-chart-h__branches">
            @foreach ($employee->subordinates as $subordinate)
                <div class="org-chart-h__branch-item">
                    @include('organization-structure.partials.employee-branch-card', ['employee' => $subordinate])
                </div>
            @endforeach
        </div>
    @endif
</div>
