@extends('layouts.app')

@section('title', 'Rekap Cuti')

@section('content')
<x-index-page
    table-id="leave-report-table"
    table-title="Rekap Cuti per Karyawan"
    title="Rekap Cuti"
    subtitle="Scope: {{ $scopeLabel }}"
    :breadcrumbs="[
        ['label' => 'Laporan', 'url' => route('reports.index')],
        ['label' => 'Rekap Cuti', 'url' => route('reports.leave')],
    ]"
>
    <x-slot:stats>
        <div class="row g-3 mb-1" id="leave-summary-cards">
            <div class="col-md-4">
                <x-stat-card label="Total Pengajuan" :value="$summary['total_requests']" icon="bx-file" icon-variant="primary" />
            </div>
            <div class="col-md-4">
                <x-stat-card label="Hari Cuti Disetujui" :value="$summary['approved_days']" icon="bx-calendar-check" icon-variant="success" />
            </div>
            <div class="col-md-4">
                <x-stat-card label="Menunggu Persetujuan" :value="$summary['pending']" icon="bx-time" icon-variant="warning" />
            </div>
        </div>
    </x-slot:stats>
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label" for="filter-year">Tahun</label>
                <input type="number" id="filter-year" class="form-control" value="{{ $year }}" min="2020" max="2100" />
            </div>
            <div class="col-md-3">
                <label class="form-label" for="filter-leave-type">Jenis Cuti</label>
                <select id="filter-leave-type" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($leaveTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->code }} — {{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="filter-status">Status</label>
                <select id="filter-status" class="form-select">
                    <option value="">Semua</option>
                    @foreach (\App\Models\LeaveRequest::statusLabels() as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if ($isHr)
            <div class="col-md-2">
                <label class="form-label" for="filter-branch">Cabang</label>
                <select id="filter-branch" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" for="filter-unit">Unit</label>
                <select id="filter-unit" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-12 col-lg-auto">
                <button type="button" id="btn-apply-filter" class="btn btn-primary">Terapkan</button>
                <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary">Reset</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            <th>Karyawan</th>
            <th>Jenis Cuti</th>
            <th>Total Hari</th>
            <th>Jumlah Pengajuan</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    const summaryUrl = @json(route('reports.leave.summary'));
    const defaultYear = @json($year);
    const defaultStatus = @json($status);
    const table = window.initServerDataTable('#leave-report-table', {
        ajax: {
            url: @json(route('reports.leave.data')),
            data: (d) => {
                d.year = document.getElementById('filter-year')?.value;
                d.leave_type_id = document.getElementById('filter-leave-type')?.value;
                d.status = document.getElementById('filter-status')?.value;
                d.branch_id = document.getElementById('filter-branch')?.value;
                d.organizational_unit_id = document.getElementById('filter-unit')?.value;
            },
        },
        order: [[0, 'asc']],
        columns: [
            { data: 'employee_display', name: 'employees.name' },
            { data: 'leave_type_display', name: 'leave_type_display' },
            { data: 'total_days_display', name: 'total_days', searchable: false },
            { data: 'request_count', name: 'request_count', searchable: false },
        ],
    });

    async function refreshSummary() {
        const params = new URLSearchParams({
            year: document.getElementById('filter-year')?.value || '',
            status: document.getElementById('filter-status')?.value || '',
        });
        const leaveType = document.getElementById('filter-leave-type')?.value;
        const branch = document.getElementById('filter-branch')?.value;
        const unit = document.getElementById('filter-unit')?.value;
        if (leaveType) params.set('leave_type_id', leaveType);
        if (branch) params.set('branch_id', branch);
        if (unit) params.set('organizational_unit_id', unit);

        const res = await fetch(`${summaryUrl}?${params.toString()}`);
        const data = await res.json();
        const cards = document.getElementById('leave-summary-cards');
        const values = [data.total_requests, data.approved_days, data.pending];
        cards?.querySelectorAll('.stat-card-value').forEach((el, i) => {
            if (values[i] !== undefined) el.textContent = values[i];
        });
    }

    document.getElementById('btn-apply-filter')?.addEventListener('click', () => {
        table.ajax.reload();
        refreshSummary();
    });
    document.getElementById('btn-reset-filter')?.addEventListener('click', () => {
        document.getElementById('filter-year').value = defaultYear;
        document.getElementById('filter-leave-type').value = '';
        document.getElementById('filter-status').value = defaultStatus;
        document.getElementById('filter-branch') && (document.getElementById('filter-branch').value = '');
        document.getElementById('filter-unit') && (document.getElementById('filter-unit').value = '');
        table.ajax.reload();
        refreshSummary();
    });
</script>
@endpush
