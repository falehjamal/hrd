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

<div class="card card-modern">
    <div class="card-header datatable-card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="datatable-card-title mb-0">Manajemen Tenant</h5>
            <p class="datatable-card-subtitle mb-0">Pantau database, status aktif, dan login terakhir</p>
        </div>
        <a href="{{ route('platform.tenants.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus me-1"></i> Tenant Baru
        </a>
    </div>
    <div class="card-body">
        <a href="{{ route('platform.tenants.index') }}" class="btn btn-outline-primary">Lihat Semua Tenant</a>
    </div>
</div>
@endsection
