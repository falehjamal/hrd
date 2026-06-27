@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
@include('partials.alerts')

<x-page-header
    title="Laporan & Analitik"
    subtitle="Rekap absensi, cuti, dan payroll"
    :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Laporan', 'url' => route('reports.index')],
    ]"
>
    <x-slot:actions>
        <span class="badge badge-pill badge-pill--info">{{ $scopeLabel }}</span>
    </x-slot:actions>
</x-page-header>

<div class="row g-4">
    <div class="col-md-4">
        <a href="{{ route('reports.attendance') }}" class="card card-modern h-100 text-decoration-none report-hub-card">
            <div class="card-body">
                <span class="report-hub-icon report-hub-icon--primary"><i class="bx bx-calendar-check"></i></span>
                <h5 class="mt-3 mb-2">Rekap Absensi</h5>
                <p class="text-muted small mb-0">Ringkasan kehadiran per karyawan berdasarkan status hadir, terlambat, alpha, dan cuti.</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reports.leave') }}" class="card card-modern h-100 text-decoration-none report-hub-card">
            <div class="card-body">
                <span class="report-hub-icon report-hub-icon--info"><i class="bx bx-calendar-minus"></i></span>
                <h5 class="mt-3 mb-2">Rekap Cuti</h5>
                <p class="text-muted small mb-0">Penggunaan cuti per karyawan dan jenis cuti dalam periode tahun tertentu.</p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('reports.payroll') }}" class="card card-modern h-100 text-decoration-none report-hub-card">
            <div class="card-body">
                <span class="report-hub-icon report-hub-icon--success"><i class="bx bx-money"></i></span>
                <h5 class="mt-3 mb-2">Rekap Payroll</h5>
                <p class="text-muted small mb-0">Ringkasan gaji per periode yang sudah difinalisasi.</p>
            </div>
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
    .report-hub-card { transition: transform 0.15s ease, box-shadow 0.15s ease; color: inherit; }
    .report-hub-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(67, 89, 113, 0.12); }
    .report-hub-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 48px; height: 48px; border-radius: 12px; font-size: 1.5rem;
    }
    .report-hub-icon--primary { background: rgba(105, 108, 255, 0.12); color: #696cff; }
    .report-hub-icon--info { background: rgba(3, 195, 236, 0.12); color: #03c3ec; }
    .report-hub-icon--success { background: rgba(113, 221, 55, 0.12); color: #71dd37; }
</style>
@endpush
