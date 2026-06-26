@extends('layouts.app')

@section('title', 'Jenis Pemotongan')

@section('content')
@include('partials.crud-open-modal')
<x-index-page
    table-id="deduction-types-table"
    table-title="Daftar Jenis Pemotongan"
    title="Jenis Pemotongan"
    subtitle="Kelola master jenis pemotongan gaji"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Jenis Pemotongan', 'url' => route('deduction-types.index')],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-primary" data-crud-create="deductionTypeFormModal">
            <i class="bx bx-plus me-1"></i> Tambah Jenis
        </button>
    </x-slot:actions>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>

<x-crud-form-modal
    modal-id="deductionTypeFormModal"
    form-id="deduction-type-form"
    route-prefix="deduction-types"
    resource-key="deduction_type"
    :open-modal="$openCrudModal ?? null"
    title-create="Tambah Jenis Pemotongan"
    title-edit="Edit Jenis Pemotongan"
    subtitle-create="Lengkapi informasi jenis pemotongan gaji."
    submit-create="Simpan Jenis"
>
    @include('deduction-types._form', ['deductionType' => null])
</x-crud-form-modal>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#deduction-types-table', {
        ajax: { url: '{{ route('deduction-types.data') }}' },
        order: [[0, 'asc']],
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'status_badge', name: 'is_active', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
