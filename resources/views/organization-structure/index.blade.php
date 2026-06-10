@extends('layouts.app')

@section('title', 'Struktur Organisasi')

@section('content')
@include('partials.alerts')

<x-page-header title="Struktur Organisasi" subtitle="Visualisasi unit organisasi dan hierarki atasan-bawahan">
    <x-slot:actions>
        <a href="{{ route('organizational-units.index') }}" class="btn btn-outline-primary me-2">
            <i class="bx bx-buildings me-1"></i> Kelola Unit
        </a>
        <a href="{{ route('positions.index') }}" class="btn btn-outline-primary">
            <i class="bx bx-briefcase me-1"></i> Kelola Jabatan
        </a>
    </x-slot:actions>
</x-page-header>

<div class="card card-modern mb-4">
    <div class="card-body">
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-units" data-bs-toggle="tab" data-bs-target="#panel-units" type="button" role="tab">
                    <i class="bx bx-buildings me-1"></i> Unit Organisasi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-reporting" data-bs-toggle="tab" data-bs-target="#panel-reporting" type="button" role="tab">
                    <i class="bx bx-git-merge me-1"></i> Atasan–Bawahan
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="panel-units" role="tabpanel">
                @if (! $companyRoot || $companyRoot->children->isEmpty())
                    <p class="text-muted mb-0">Belum ada unit organisasi. <a href="{{ route('organizational-units.create') }}">Tambah unit</a></p>
                @else
                    <div class="org-chart-scroll">
                        @include('organization-structure.partials.horizontal-unit-chart', ['companyRoot' => $companyRoot])
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="panel-reporting" role="tabpanel">
                @if ($reportingTree->isEmpty())
                    <p class="text-muted mb-0">Belum ada hierarki atasan. Atur atasan langsung di data karyawan.</p>
                @else
                    <div class="org-chart-scroll">
                        @include('organization-structure.partials.horizontal-reporting-chart', ['reportingTree' => $reportingTree])
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card card-modern org-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="org-stat-card__icon org-stat-card__icon--employees">
                    <i class="bx bx-group"></i>
                </span>
                <div>
                    <span class="org-stat-card__label">Total Pegawai</span>
                    <h3 class="org-stat-card__value mb-0">{{ number_format($stats['total_employees'], 0, ',', '.') }}</h3>
                    <span class="org-stat-card__hint text-muted">Karyawan aktif</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-modern org-stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="org-stat-card__icon org-stat-card__icon--departments">
                    <i class="bx bx-buildings"></i>
                </span>
                <div>
                    <span class="org-stat-card__label">Total Departemen</span>
                    <h3 class="org-stat-card__value mb-0">{{ number_format($stats['total_departments'], 0, ',', '.') }}</h3>
                    <span class="org-stat-card__hint text-muted">Unit organisasi aktif</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .org-chart-scroll {
        overflow-x: auto;
        padding: 0.5rem 0 1rem;
    }

    .org-chart-h {
        display: flex;
        align-items: stretch;
        gap: 0;
        min-width: max-content;
        padding: 1rem 0.5rem;
    }

    .org-chart-h__root {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .org-root-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        min-width: 11rem;
        max-width: 11rem;
        min-height: 8.5rem;
        padding: 1.25rem 1rem;
        border-radius: 0.75rem;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: #fff;
        box-shadow: 0 4px 14px rgba(34, 197, 94, 0.25);
    }

    .org-root-card__icon {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
        opacity: 0.95;
    }

    .org-root-card__title {
        font-weight: 700;
        font-size: 0.8rem;
        line-height: 1.35;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .org-root-card__subtitle {
        font-size: 0.65rem;
        opacity: 0.85;
        margin-top: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .org-chart-h__spine {
        position: relative;
        width: 2.5rem;
        flex-shrink: 0;
        align-self: stretch;
    }

    .org-chart-h__spine::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 2px;
        background: #cbd5e1;
        transform: translateY(-50%);
    }

    .org-chart-h__spine::after {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 2px;
        background: #cbd5e1;
    }

    .org-chart-h__departments {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        flex-shrink: 0;
    }

    .org-chart-h__row {
        display: flex;
        align-items: center;
        gap: 0;
        position: relative;
    }

    .org-chart-h__row::before {
        content: '';
        position: absolute;
        left: -2.5rem;
        top: 50%;
        width: 2.5rem;
        height: 2px;
        background: #cbd5e1;
    }

    .org-chart-h__unit {
        flex-shrink: 0;
    }

    .org-unit-card {
        min-width: 13.5rem;
        max-width: 13.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #f8fafc;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .org-unit-card__header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 0.85rem;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }

    .org-unit-card__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 0.35rem;
        background: rgba(34, 197, 94, 0.12);
        color: #16a34a;
        font-size: 0.95rem;
    }

    .org-unit-card__title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #16a34a;
        line-height: 1.2;
    }

    .org-unit-card__body {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 1rem 0.85rem 1.1rem;
        color: inherit;
    }

    .org-unit-card__body:hover {
        background: rgba(34, 197, 94, 0.04);
    }

    .org-unit-card__body--empty {
        min-height: 5rem;
        justify-content: center;
    }

    .org-unit-card__avatar,
    .org-employee-branch__avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        font-size: 1.35rem;
        margin-bottom: 0.5rem;
    }

    .org-unit-card__name,
    .org-employee-branch__name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1e293b;
        line-height: 1.3;
    }

    .org-unit-card__code {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.15rem;
        margin-bottom: 0.55rem;
    }

    .org-position-badge {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 0.35rem;
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .org-badge-dir { background: #dcfce7; color: #15803d; }
    .org-badge-mgr { background: #dbeafe; color: #1d4ed8; }
    .org-badge-spv { background: #ccfbf1; color: #0f766e; }
    .org-badge-stf { background: #e0f2fe; color: #0369a1; }

    .org-chart-h__branch-spine {
        position: relative;
        width: 2rem;
        flex-shrink: 0;
        align-self: stretch;
        min-height: 2rem;
    }

    .org-chart-h__branch-spine::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 2px;
        background: #cbd5e1;
    }

    .org-chart-h__branches {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        flex-shrink: 0;
    }

    .org-employee-branch {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        min-width: 10rem;
        max-width: 10rem;
        padding: 0.85rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        background: #fff;
        color: inherit;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .org-employee-branch:hover {
        border-color: #86efac;
        background: #f0fdf4;
    }

    .org-employee-branch--boxed {
        min-width: 13.5rem;
        max-width: 13.5rem;
        padding: 1rem 0.85rem;
    }

    .org-chart-h--reporting-block {
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed #e2e8f0;
    }

    .org-chart-h--reporting-block:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .org-stat-card__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 0.75rem;
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .org-stat-card__icon--employees {
        background: rgba(105, 108, 255, 0.12);
        color: #696cff;
    }

    .org-stat-card__icon--departments {
        background: rgba(34, 197, 94, 0.12);
        color: #22c55e;
    }

    .org-stat-card__label {
        display: block;
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.15rem;
    }

    .org-stat-card__value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
    }

    .org-stat-card__hint {
        font-size: 0.75rem;
    }

    .dark-style .org-unit-card {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .dark-style .org-unit-card__header {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .dark-style .org-unit-card__name,
    .dark-style .org-employee-branch__name,
    .dark-style .org-stat-card__value {
        color: #e2e8f0;
    }

    .dark-style .org-employee-branch {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.08);
    }
</style>
@endpush
