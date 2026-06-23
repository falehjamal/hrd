@extends('layouts.app')

@section('title', 'Jenis Cuti')

@section('content')
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
        <a href="{{ route('leave-types.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Jenis Cuti
        </a>
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
