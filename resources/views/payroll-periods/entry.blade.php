@extends('layouts.app')

@section('title', 'Slip Gaji')

@section('content')
@include('partials.alerts')

<x-page-header title="Slip Gaji" subtitle="{{ $entry->employee->employee_code }} — {{ $entry->employee->name }}">
    <x-slot:actions>
        <button type="button" class="btn btn-outline-primary" onclick="window.print()">
            <i class="bx bx-printer me-1"></i> Cetak
        </button>
        <a href="{{ $backRoute }}" class="btn btn-outline-secondary">Kembali</a>
    </x-slot:actions>
</x-page-header>

@include('payroll-periods._payslip-detail')
@endsection

@push('styles')
<style>
@media print {
    #layout-menu, .layout-navbar, .content-footer, .btn, .page-header-actions { display: none !important; }
    .layout-page { padding: 0 !important; }
}
</style>
@endpush
