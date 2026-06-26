@extends('layouts.app')

@section('title', 'Data Shift')

@section('content')
@include('partials.crud-open-modal')
<x-index-page
    table-id="shifts-table"
    table-title="Daftar Shift"
    title="Data Shift"
    subtitle="Kelola jadwal kerja karyawan"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Data Shift', 'url' => route('shifts.index')],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-primary" data-crud-create="shiftFormModal">
            <i class="bx bx-plus me-1"></i> Tambah Shift
        </button>
    </x-slot:actions>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Jam Kerja</th>
            <th>Istirahat</th>
            <th>Karyawan</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>

<x-crud-form-modal
    modal-id="shiftFormModal"
    form-id="shift-form"
    route-prefix="shifts"
    resource-key="shift"
    :open-modal="$openCrudModal ?? null"
    title-create="Tambah Shift Baru"
    title-edit="Edit Shift"
    subtitle-create="Lengkapi informasi jadwal kerja shift."
    submit-create="Simpan Shift"
>
    @include('shifts._form', ['shift' => null])
</x-crud-form-modal>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#shifts-table', {
        ajax: { url: '{{ route('shifts.data') }}' },
        order: [[0, 'asc']],
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'work_hours', name: 'start_time', orderable: false, searchable: false },
            { data: 'break_display', name: 'break_minutes', orderable: false, searchable: false },
            { data: 'employees_count', name: 'employees_count', searchable: false },
            { data: 'status_badge', name: 'is_active', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
