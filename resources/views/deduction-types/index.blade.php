@extends('layouts.app')

@section('title', 'Jenis Pemotongan')

@section('content')
@include('partials.alerts')

<x-page-header title="Jenis Pemotongan" subtitle="Kelola master jenis pemotongan gaji">
    <x-slot:actions>
        <a href="{{ route('deduction-types.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Jenis
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="deduction-types-table" title="Daftar Jenis Pemotongan">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
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
