@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<x-index-page
    table-id="attendance-report-table"
    table-title="Rekap Absensi per Karyawan"
    title="Rekap Absensi"
    subtitle="Scope: {{ $scopeLabel }}"
    :breadcrumbs="[
        ['label' => 'Laporan', 'url' => route('reports.index')],
        ['label' => 'Rekap Absensi', 'url' => route('reports.attendance')],
    ]"
>
    <x-slot:stats>
        <div class="row g-3 mb-1" id="attendance-summary-cards">
            <div class="col-sm-6 col-xl">
                <x-stat-card label="Hadir" :value="$summary['present']" icon="bx-check-circle" icon-variant="success" />
            </div>
            <div class="col-sm-6 col-xl">
                <x-stat-card label="Terlambat" :value="$summary['late']" icon="bx-time-five" icon-variant="warning" />
            </div>
            <div class="col-sm-6 col-xl">
                <x-stat-card label="Alpha" :value="$summary['absent']" icon="bx-x-circle" icon-variant="danger" />
            </div>
            <div class="col-sm-6 col-xl">
                <x-stat-card label="Cuti" :value="$summary['leave']" icon="bx-calendar-minus" icon-variant="info" />
            </div>
            <div class="col-sm-6 col-xl">
                <x-stat-card label="Total Tercatat" :value="$summary['total']" icon="bx-list-check" icon-variant="primary" />
            </div>
        </div>
    </x-slot:stats>
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="filter-date-from">Dari Tanggal</label>
                <input type="date" id="filter-date-from" class="form-control" value="{{ $dateFrom }}" />
            </div>
            <div class="col-md-3">
                <label class="form-label" for="filter-date-to">Sampai Tanggal</label>
                <input type="date" id="filter-date-to" class="form-control" value="{{ $dateTo }}" />
            </div>
            @if ($isHr)
            <div class="col-md-3">
                <label class="form-label" for="filter-branch">Cabang</label>
                <select id="filter-branch" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
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
            <th>Unit</th>
            <th>Cabang</th>
            <th>Hadir</th>
            <th>Terlambat</th>
            <th>Alpha</th>
            <th>Cuti</th>
            <th>Setengah Hari</th>
            <th>Total</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    const summaryUrl = @json(route('reports.attendance.summary'));
    const table = window.initServerDataTable('#attendance-report-table', {
        ajax: {
            url: @json(route('reports.attendance.data')),
            data: (d) => {
                d.date_from = document.getElementById('filter-date-from')?.value;
                d.date_to = document.getElementById('filter-date-to')?.value;
                d.branch_id = document.getElementById('filter-branch')?.value;
                d.organizational_unit_id = document.getElementById('filter-unit')?.value;
            },
        },
        order: [[0, 'asc']],
        columns: [
            { data: 'employee_display', name: 'employees.name' },
            { data: 'unit_display', name: 'unit_display', orderable: false },
            { data: 'branch_display', name: 'branch_display', orderable: false },
            { data: 'present_count', name: 'present_count', searchable: false },
            { data: 'late_count', name: 'late_count', searchable: false },
            { data: 'absent_count', name: 'absent_count', searchable: false },
            { data: 'leave_count', name: 'leave_count', searchable: false },
            { data: 'half_day_count', name: 'half_day_count', searchable: false },
            { data: 'total_recorded', name: 'total_recorded', searchable: false },
        ],
    });

    async function refreshSummary() {
        const params = new URLSearchParams({
            date_from: document.getElementById('filter-date-from')?.value || '',
            date_to: document.getElementById('filter-date-to')?.value || '',
        });
        const branch = document.getElementById('filter-branch')?.value;
        const unit = document.getElementById('filter-unit')?.value;
        if (branch) params.set('branch_id', branch);
        if (unit) params.set('organizational_unit_id', unit);

        const res = await fetch(`${summaryUrl}?${params.toString()}`);
        const data = await res.json();
        const cards = document.getElementById('attendance-summary-cards');
        const values = [data.present, data.late, data.absent, data.leave, data.total];
        cards?.querySelectorAll('.stat-card-value').forEach((el, i) => {
            if (values[i] !== undefined) el.textContent = values[i];
        });
    }

    document.getElementById('btn-apply-filter')?.addEventListener('click', () => {
        table.ajax.reload();
        refreshSummary();
    });
    document.getElementById('btn-reset-filter')?.addEventListener('click', () => {
        document.getElementById('filter-date-from').value = @json($dateFrom);
        document.getElementById('filter-date-to').value = @json($dateTo);
        document.getElementById('filter-branch') && (document.getElementById('filter-branch').value = '');
        document.getElementById('filter-unit') && (document.getElementById('filter-unit').value = '');
        table.ajax.reload();
        refreshSummary();
    });
</script>
@endpush
