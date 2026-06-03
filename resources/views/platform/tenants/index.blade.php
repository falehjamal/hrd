@extends('layouts.platform')

@section('title', 'Daftar Tenant')

@section('content')
@include('partials.alerts')

<x-page-header title="Daftar Tenant" subtitle="Pantau status dan aktivitas perusahaan">
    <x-slot:actions>
        <a href="{{ route('platform.tenants.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tenant Baru
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="tenants-table" title="Semua Tenant">
    <thead>
        <tr>
            <th>Perusahaan</th>
            <th>ID / DB</th>
            <th>Status</th>
            <th>User</th>
            <th>Login Terakhir</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#tenants-table', {
        ajax: { url: '{{ route('platform.tenants.data') }}' },
        order: [[0, 'asc']],
        columns: [
            { data: 'company_name', name: 'name', orderable: true, searchable: true },
            { data: 'database_name', name: 'id', orderable: true, searchable: true },
            { data: 'status_badge', name: 'status', orderable: true, searchable: false },
            { data: 'users_count_display', name: 'tenant_users_count', searchable: false },
            { data: 'last_login_display', name: 'last_login_at', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
