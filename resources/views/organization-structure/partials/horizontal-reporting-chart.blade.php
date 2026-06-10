@foreach ($reportingTree as $rootEmployee)
    <div class="org-chart-h org-chart-h--reporting-block mb-4">
        <div class="org-chart-h__root">
            @include('organization-structure.partials.employee-leader-card', ['employee' => $rootEmployee])
        </div>

        @if ($rootEmployee->subordinates->isNotEmpty())
            <div class="org-chart-h__spine" aria-hidden="true"></div>
            <div class="org-chart-h__departments">
                @foreach ($rootEmployee->subordinates as $subordinate)
                    @include('organization-structure.partials.reporting-subordinate-row', ['employee' => $subordinate])
                @endforeach
            </div>
        @endif
    </div>
@endforeach
