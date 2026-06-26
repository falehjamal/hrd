@extends('layouts.app')

@section('title', 'Jenis Cuti')

@section('content')
@include('partials.crud-open-modal')
<x-index-page
    table-id="leave-types-table"
    table-title="Daftar Jenis Cuti"
    title="Jenis Cuti"
    subtitle="Kelola master jenis cuti karyawan"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Jenis Cuti', 'url' => route('leave-types.index')],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-primary" data-crud-create="leaveTypeFormModal">
            <i class="bx bx-plus me-1"></i> Tambah Jenis Cuti
        </button>
    </x-slot:actions>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kuota Default</th>
            <th>Tipe</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>

<x-crud-form-modal
    modal-id="leaveTypeFormModal"
    form-id="leave-type-form"
    route-prefix="leave-types"
    resource-key="leave_type"
    :open-modal="$openCrudModal ?? null"
    title-create="Tambah Jenis Cuti"
    title-edit="Edit Jenis Cuti"
    subtitle-create="Lengkapi informasi jenis cuti karyawan."
    submit-create="Simpan Jenis Cuti"
>
    @include('leave-types._form', ['leaveType' => null])
</x-crud-form-modal>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#leave-types-table', {
        ajax: { url: '{{ route('leave-types.data') }}' },
        order: [[0, 'asc']],
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'default_quota_days', name: 'default_quota_days' },
            { data: 'paid_badge', name: 'is_paid', orderable: false, searchable: false },
            { data: 'status_badge', name: 'is_active', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
