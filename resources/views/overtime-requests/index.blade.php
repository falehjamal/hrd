@extends('layouts.app')

@section('title', 'Lembur')

@section('content')
@include('partials.crud-open-modal')
<x-index-page
    table-id="overtime-table"
    table-title="Daftar Pengajuan Lembur"
    title="Lembur"
    subtitle="{{ $isEmployee ? 'Pengajuan lembur Anda' : 'Kelola persetujuan lembur karyawan' }}"
    :breadcrumbs="[
        ['label' => 'Operasional', 'url' => route('attendances.index')],
        ['label' => 'Lembur', 'url' => route('overtime-requests.index')],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-primary" data-crud-create="overtimeFormModal">
            <i class="bx bx-plus me-1"></i> Ajukan Lembur
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
                <label class="form-label" for="filter-status">Status</label>
                <select id="filter-status" class="form-select">
                    <option value="">Semua</option>
                    @foreach (\App\Models\OvertimeRequest::statusLabels() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" id="btn-apply-filter" class="btn btn-primary">Terapkan</button>
                <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary">Reset</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            @if (!$isEmployee)<th>Karyawan</th>@endif
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Durasi</th>
            <th>Alasan</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>

<x-crud-form-modal
    modal-id="overtimeFormModal"
    form-id="overtime-form"
    route-prefix="overtime-requests"
    :open-modal="$openCrudModal ?? null"
    title-create="Ajukan Lembur"
    subtitle-create="Form pengajuan lembur"
    submit-create="Kirim Pengajuan"
>
    @include('overtime-requests._form')
</x-crud-form-modal>
@endsection

@push('datatable-scripts')
<script type="module">
    const columns = [
        @if (! $isEmployee)
        { data: 'employee_display', name: 'employee.name' },
        @endif
        { data: 'date_display', name: 'date' },
        { data: 'time_range', name: 'start_time', orderable: false, searchable: false },
        { data: 'duration_display', name: 'duration_minutes', searchable: false },
        { data: 'reason', name: 'reason' },
        { data: 'status_badge', name: 'status', searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
    ];
    const table = window.initServerDataTable('#overtime-table', {
        ajax: {
            url: '{{ route('overtime-requests.data') }}',
            data: (d) => {
                d.date_from = document.getElementById('filter-date-from')?.value;
                d.date_to = document.getElementById('filter-date-to')?.value;
                d.status = document.getElementById('filter-status')?.value;
            },
        },
        order: [[{{ $isEmployee ? 0 : 1 }}, 'desc']],
        columns,
    });
    document.getElementById('btn-apply-filter')?.addEventListener('click', () => table.ajax.reload());
    document.getElementById('btn-reset-filter')?.addEventListener('click', () => {
        document.getElementById('filter-date-from').value = '';
        document.getElementById('filter-date-to').value = '';
        document.getElementById('filter-status').value = '';
        table.ajax.reload();
    });
</script>
@endpush
