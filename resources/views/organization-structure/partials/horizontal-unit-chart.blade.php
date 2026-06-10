@php
    $departments = $companyRoot->children;
@endphp

<div class="org-chart-h">
    <div class="org-chart-h__root">
        <div class="org-root-card">
            <span class="org-root-card__icon"><i class="bx bx-buildings"></i></span>
            <span class="org-root-card__title">{{ $companyRoot->name }}</span>
            <span class="org-root-card__subtitle">Akar Organisasi</span>
        </div>
    </div>

    @if ($departments->isNotEmpty())
        <div class="org-chart-h__spine" aria-hidden="true"></div>

        <div class="org-chart-h__departments">
            @foreach ($departments as $unit)
                @php
                    $primaryEmployee = $unit->employees
                        ->sortBy(fn ($employee) => $employee->position?->level ?? 99)
                        ->first();
                    $otherEmployees = $unit->employees
                        ->when($primaryEmployee, fn ($employees) => $employees->where('id', '!=', $primaryEmployee->id))
                        ->values();
                    $hasBranches = $unit->children->isNotEmpty() || $otherEmployees->isNotEmpty();
                @endphp

                <div class="org-chart-h__row {{ $hasBranches ? 'org-chart-h__row--branch' : '' }}">
                    <div class="org-chart-h__unit">
                        @include('organization-structure.partials.unit-card', ['unit' => $unit])
                    </div>

                    @if ($hasBranches)
                        <div class="org-chart-h__branch-spine" aria-hidden="true"></div>
                        <div class="org-chart-h__branches">
                            @foreach ($otherEmployees as $employee)
                                <div class="org-chart-h__branch-item">
                                    @include('organization-structure.partials.employee-branch-card', ['employee' => $employee])
                                </div>
                            @endforeach

                            @foreach ($unit->children as $child)
                                <div class="org-chart-h__branch-item org-chart-h__branch-item--unit">
                                    @include('organization-structure.partials.unit-card', ['unit' => $child])
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
