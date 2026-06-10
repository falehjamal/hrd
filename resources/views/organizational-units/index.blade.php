@extends('layouts.app')

@section('title', 'Unit Organisasi')

@section('content')
@include('partials.alerts')

<x-page-header title="Unit Organisasi" subtitle="Kelola departemen dan divisi perusahaan">
    <x-slot:actions>
        <a href="{{ route('organization-structure.index') }}" class="btn btn-outline-primary me-2">
            <i class="bx bx-sitemap me-1"></i> Struktur Organisasi
        </a>
        <a href="{{ route('organizational-units.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Unit
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="organizational-units-table" title="Daftar Unit Organisasi">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Unit Induk</th>
            <th>Karyawan</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#organizational-units-table', {
        ajax: { url: '{{ route('organizational-units.data') }}' },
        order: [[1, 'asc']],
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'parent_name', name: 'parent.name', defaultContent: '-' },
            { data: 'employees_count', name: 'employees_count', searchable: false },
            { data: 'status_badge', name: 'is_active', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
