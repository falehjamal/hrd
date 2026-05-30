@extends('layouts.platform')

@section('title', $tenant->displayName())

@section('content')
@include('partials.alerts')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $tenant->displayName() }}</h4>
        <p class="text-muted mb-0">ID: <code>{{ $tenant->id }}</code> · Slug: {{ $tenant->slug }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('platform.tenants.edit', $tenant) }}" class="btn btn-outline-primary btn-sm">Edit</a>
        @if ($tenant->isActive())
            <form action="{{ route('platform.tenants.suspend', $tenant) }}" method="POST" onsubmit="return confirm('Nonaktifkan tenant ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-danger btn-sm">Nonaktifkan</button>
            </form>
        @else
            <form action="{{ route('platform.tenants.activate', $tenant) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-success btn-sm">Aktifkan</button>
            </form>
        @endif
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <small class="text-muted">Status</small>
                <div class="mt-1">
                    @if ($tenant->isActive())
                        <span class="badge bg-label-success">Aktif</span>
                    @else
                        <span class="badge bg-label-danger">Nonaktif sejak {{ $tenant->suspended_at?->format('d M Y H:i') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
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
        <div class="card">
            <div class="card-body">
                <small class="text-muted">User (tenant DB)</small>
                <h5 class="mb-0 mt-1">{{ $metrics['users_count'] }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
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

<div class="card">
    <div class="card-header"><h5 class="mb-0">Pengguna terdaftar (central)</h5></div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Login terakhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tenantUsers as $tu)
                    <tr>
                        <td>{{ $tu->email }}</td>
                        <td>{{ $tu->username ?? '—' }}</td>
                        <td>{{ $tu->last_login_at?->format('d M Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<a href="{{ route('platform.tenants.index') }}" class="btn btn-secondary mt-3">Kembali</a>
@endsection
