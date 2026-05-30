@extends('layouts.platform')

@section('title', 'Daftar Tenant')

@section('content')
@include('partials.alerts')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Tenant</h5>
        <a href="{{ route('platform.tenants.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus me-1"></i> Tenant Baru
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Perusahaan</th>
                    <th>ID / DB</th>
                    <th>Status</th>
                    <th>Ukuran DB</th>
                    <th>User</th>
                    <th>Login Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tenants as $row)
                    @php($tenant = $row->tenant)
                    @php($metrics = $row->metrics)
                    <tr>
                        <td>
                            <strong>{{ $tenant->displayName() }}</strong>
                            @if ($tenant->app_title)
                                <br><small class="text-muted">{{ $tenant->name }}</small>
                            @endif
                        </td>
                        <td>
                            <code>{{ $tenant->id }}</code>
                            <br><small class="text-muted">{{ $metrics['database'] }}</small>
                        </td>
                        <td>
                            @if ($tenant->isActive())
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            @if ($metrics['database_exists'])
                                {{ $metrics['size_mb'] ?? '—' }} MB
                            @else
                                <span class="text-danger">DB belum ada</span>
                            @endif
                        </td>
                        <td>{{ $metrics['users_count'] }}</td>
                        <td>
                            @if ($metrics['last_login_at'])
                                {{ \Carbon\Carbon::parse($metrics['last_login_at'])->diffForHumans() }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('platform.tenants.show', $tenant) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-show"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada tenant.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
