@extends('layouts.app')

@section('title', 'Jenis Pemotongan')

@section('content')
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
        <a href="{{ route('deduction-types.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Jenis
        </a>
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
