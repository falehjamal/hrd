@extends('layouts.app')

@section('title', 'Data Jabatan')

@section('content')
<x-index-page
    table-id="positions-table"
    table-title="Daftar Jabatan"
    title="Data Jabatan"
    subtitle="Kelola master data jabatan karyawan"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Data Jabatan', 'url' => route('positions.index')],
    ]"
>
    <x-slot:actions>
        <a href="{{ route('positions.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Jabatan
        </a>
    </x-slot:actions>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Level</th>
            <th>Karyawan</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#positions-table', {
        ajax: { url: '{{ route('positions.data') }}' },
        order: [[2, 'asc'], [0, 'asc']],
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'level', name: 'level' },
            { data: 'employees_count', name: 'employees_count', searchable: false },
            { data: 'status_badge', name: 'is_active', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
