@extends('layouts.platform')

@section('title', 'Dashboard Platform')

@section('content')
@include('partials.alerts')

<div class="dashboard-hero mb-4">
    <div class="dashboard-hero-content">
        <span class="dashboard-hero-badge">Platform</span>
        <h2 class="dashboard-hero-title">Dashboard Platform</h2>
        <p class="dashboard-hero-text">Pantau tenant terdaftar, status aktif, dan kesehatan sistem multi-tenant.</p>
        <a href="{{ route('platform.tenants.create') }}" class="btn btn-light btn-hero mt-3">
            <i class="bx bx-plus me-1"></i> Tenant Baru
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <x-stat-card label="Total Tenant" :value="$stats['total']" icon="bx-buildings" icon-variant="primary" />
    </div>
    <div class="col-md-4">
        <x-stat-card label="Aktif" :value="$stats['active']" icon="bx-check-circle" icon-variant="success"
            :progress="$stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0" progress-variant="success" />
    </div>
    <div class="col-md-4">
        <x-stat-card label="Nonaktif" :value="$stats['suspended']" icon="bx-x-circle" icon-variant="danger" />
    </div>
</div>

<x-datatable-card tableId="tenants-preview-table" title="Tenant Terbaru" subtitle="Preview tenant terdaftar">
    <x-slot:headerActions>
        <a href="{{ route('platform.tenants.index') }}" class="btn btn-sm btn-outline-primary">
            Lihat Semua <i class="bx bx-right-arrow-alt ms-1"></i>
        </a>
    </x-slot:headerActions>
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
    window.initServerDataTable('#tenants-preview-table', {
        ajax: { url: '{{ route('platform.tenants.data') }}' },
        pageLength: 5,
        lengthChange: false,
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
