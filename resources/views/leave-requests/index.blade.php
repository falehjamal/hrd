@extends('layouts.app')

@section('title', 'Cuti')

@section('content')
@include('partials.crud-open-modal')
<x-index-page
    table-id="leave-requests-table"
    table-title="Daftar Pengajuan Cuti"
    title="Cuti"
    subtitle="{{ $isEmployee ? 'Pengajuan cuti Anda' : 'Kelola persetujuan cuti karyawan' }}"
    :breadcrumbs="[
        ['label' => 'Operasional', 'url' => route('attendances.index')],
        ['label' => 'Cuti', 'url' => route('leave-requests.index')],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-primary" data-crud-create="leaveFormModal">
            <i class="bx bx-plus me-1"></i> Ajukan Cuti
        </button>
    </x-slot:actions>
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="filter-date-from">Dari Tanggal</label>
                <input type="date" id="filter-date-from" class="form-control" />
            </div>
            <div class="col-md-3">
                <label class="form-label" for="filter-date-to">Sampai Tanggal</label>
                <input type="date" id="filter-date-to" class="form-control" />
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
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 col-lg-auto">
                <button type="button" id="btn-apply-filter" class="btn btn-primary">Terapkan</button>
                <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary">Reset</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            @if (!$isEmployee)<th>Karyawan</th>@endif
            <th>Jenis Cuti</th>
            <th>Rentang</th>
            <th>Total Hari</th>
            <th>Alasan</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>

<x-crud-form-modal
    modal-id="leaveFormModal"
    form-id="leave-form"
    route-prefix="leave-requests"
    :open-modal="$openCrudModal ?? null"
    enctype="multipart/form-data"
    title-create="Ajukan Cuti"
    subtitle-create="Form pengajuan cuti"
    submit-create="Kirim Pengajuan"
>
    @include('leave-requests._form')
</x-crud-form-modal>
@endsection

@push('datatable-scripts')
<script type="module">
    const columns = [
        @if (! $isEmployee)
        { data: 'employee_display', name: 'employee.name' },
        @endif
        { data: 'leave_type_display', name: 'leave_type_display' },
        { data: 'date_range', name: 'start_date' },
        { data: 'total_days_display', name: 'total_days', searchable: false },
        { data: 'reason', name: 'reason' },
        { data: 'status_badge', name: 'status', searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
    ];
    const table = window.initServerDataTable('#leave-requests-table', {
        ajax: {
            url: '{{ route('leave-requests.data') }}',
            data: (d) => {
                d.date_from = document.getElementById('filter-date-from')?.value;
                d.date_to = document.getElementById('filter-date-to')?.value;
                d.status = document.getElementById('filter-status')?.value;
                d.leave_type_id = document.getElementById('filter-leave-type')?.value;
            },
        },
        order: [[{{ $isEmployee ? 2 : 3 }}, 'desc']],
        columns,
    });
    document.getElementById('btn-apply-filter')?.addEventListener('click', () => table.ajax.reload());
    document.getElementById('btn-reset-filter')?.addEventListener('click', () => {
        document.getElementById('filter-date-from').value = '';
        document.getElementById('filter-date-to').value = '';
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-leave-type').value = '';
        table.ajax.reload();
    });

    const employeeField = document.getElementById('employee_id');
    const startField = document.getElementById('start_date');
    const endField = document.getElementById('end_date');
    const preview = document.getElementById('leave-days-preview');
    const countEl = document.getElementById('leave-days-count');

    async function updatePreview() {
        const employeeId = employeeField?.value;
        const startDate = startField?.value;
        const endDate = endField?.value;

        if (!employeeId || !startDate || !endDate) {
            preview.style.display = 'none';
            return;
        }

        const params = new URLSearchParams({ employee_id: employeeId, start_date: startDate, end_date: endDate });
        const response = await fetch(`{{ route('leave-requests.calculate-days') }}?${params.toString()}`);
        const data = await response.json();

        countEl.textContent = data.total_days ?? 0;
        preview.style.display = 'block';
    }

    [employeeField, startField, endField].forEach((el) => {
        el?.addEventListener('change', updatePreview);
    });

    document.getElementById('leaveFormModal')?.addEventListener('shown.bs.modal', updatePreview);
    document.getElementById('leave-form')?.addEventListener('crud-form:reset', () => {
        preview.style.display = 'none';
    });
</script>
@endpush
