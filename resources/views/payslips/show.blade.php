@extends('layouts.app')

@section('title', 'Slip Gaji')

@section('content')
@include('partials.alerts')

<x-page-header
    title="Slip Gaji"
    :subtitle="$period->periodLabel()"
    :breadcrumbs="[
        ['label' => 'Slip Gaji', 'url' => route('payslips.index')],
        ['label' => $period->periodLabel(), 'url' => url()->current()],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Cetak
        </button>
        <a href="{{ $backRoute }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </x-slot:actions>
</x-page-header>

<div class="card card-modern content-card">
    <div class="card-body">
        @include('payroll-periods._payslip-detail')
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    #layout-menu, .layout-navbar, .content-footer, .btn, .page-header-actions { display: none !important; }
    .layout-page { padding: 0 !important; }
    .content-card { box-shadow: none !important; border: none !important; }
}
</style>
@endpush
