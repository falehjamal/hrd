@extends('layouts.platform')

@section('title', $tenant->displayName())

@section('content')
@include('partials.alerts')

<x-page-header :title="$tenant->displayName()" :subtitle="'ID: '.$tenant->id.' · Slug: '.$tenant->slug">
    <x-slot:actions>
        <a href="{{ route('platform.tenants.edit', $tenant) }}" class="btn btn-outline-primary btn-sm">Edit</a>
        @if ($tenant->isActive())
            <form action="{{ route('platform.tenants.suspend', $tenant) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-danger btn-sm">Nonaktifkan</button>
            </form>
        @else
            <form action="{{ route('platform.tenants.activate', $tenant) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-success btn-sm">Aktifkan</button>
            </form>
        @endif
    </x-slot:actions>
</x-page-header>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card card-modern">
            <div class="card-body">
                <small class="text-muted">Status</small>
                <div class="mt-1">
                    @if ($tenant->isActive())
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-danger">Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern">
            <div class="card-body">
                <small class="text-muted">Database</small>
                <p class="mb-0 mt-1"><code>{{ $metrics['database'] }}</code></p>
                @if ($metrics['database_exists'])
                    <small>{{ $metrics['size_mb'] ?? '—' }} MB</small>
                @else
                    <small class="text-danger">Tidak ditemukan</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern">
            <div class="card-body">
                <small class="text-muted">User (tenant DB)</small>
                <h5 class="mb-0 mt-1">{{ $metrics['users_count'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-modern">
            <div class="card-body">
                <small class="text-muted">Login terakhir</small>
                <p class="mb-0 mt-1">
                    @if ($metrics['last_login_at'])
                        {{ \Carbon\Carbon::parse($metrics['last_login_at'])->format('d M Y H:i') }}
                    @else
                        —
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<x-datatable-card tableId="tenant-users-table" title="Pengguna terdaftar (central)">
    <thead>
        <tr>
            <th>Email</th>
            <th>Username</th>
            <th>Login terakhir</th>
        </tr>
    </thead>
</x-datatable-card>

<a href="{{ route('platform.tenants.index') }}" class="btn btn-secondary mt-3">Kembali</a>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#tenant-users-table', {
        ajax: { url: '{{ route('platform.tenants.users.data', $tenant) }}' },
        order: [[2, 'desc']],
        buttons: [],
        columns: [
            { data: 'email', name: 'email' },
            { data: 'username', name: 'username', defaultContent: '—' },
            { data: 'last_login_display', name: 'last_login_at', orderable: true, searchable: false },
        ],
    });
</script>
@endpush
