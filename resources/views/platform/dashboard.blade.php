@extends('layouts.platform')

@section('title', 'Dashboard Platform')

@section('content')
@include('partials.alerts')

<x-page-header title="Dashboard Platform" subtitle="Ringkasan tenant terdaftar" />

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-modern">
            <div class="card-body">
                <span class="text-muted d-block mb-1">Total Tenant</span>
                <h3 class="card-title mb-0">{{ $stats['total'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern">
            <div class="card-body">
                <span class="text-muted d-block mb-1">Aktif</span>
                <h3 class="card-title mb-0 text-success">{{ $stats['active'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-modern">
            <div class="card-body">
                <span class="text-muted d-block mb-1">Nonaktif</span>
                <h3 class="card-title mb-0 text-danger">{{ $stats['suspended'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card card-modern">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manajemen Tenant</h5>
        <a href="{{ route('platform.tenants.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus me-1"></i> Tenant Baru
        </a>
    </div>
    <div class="card-body">
        <p class="mb-3 text-muted">Pantau database, status aktif, dan login terakhir setiap perusahaan.</p>
        <a href="{{ route('platform.tenants.index') }}" class="btn btn-outline-primary">Lihat Semua Tenant</a>
    </div>
</div>
@endsection
