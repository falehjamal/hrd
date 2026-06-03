@extends('layouts.app')

@section('title', 'Data Shift')

@section('content')
@include('partials.alerts')

<x-page-header title="Data Shift" subtitle="Kelola jadwal kerja karyawan">
    <x-slot:actions>
        <a href="{{ route('shifts.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Shift
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="shifts-table" title="Daftar Shift">
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
</x-datatable-card>
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
