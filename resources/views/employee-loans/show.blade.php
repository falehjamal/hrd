@extends('layouts.app')

@section('title', 'Detail Piutang')

@section('content')
@include('partials.alerts')

<x-page-header
    title="Detail Piutang"
    :subtitle="$loan->employee->employee_code.' — '.$loan->employee->name"
    :breadcrumbs="[
        ['label' => 'Piutang Karyawan', 'url' => route('employee-loans.index')],
        ['label' => $loan->loan_date->format('d/m/Y'), 'url' => route('employee-loans.show', $loan)],
    ]"
>
    <x-slot:actions>
        @if ($loan->status === \App\Models\EmployeeLoan::STATUS_ACTIVE)
            <form action="{{ route('employee-loans.cancel', $loan) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-warning">Batalkan</button>
            </form>
        @endif
        <a href="{{ route('employee-loans.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </x-slot:actions>
</x-page-header>

<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card card-modern content-card h-100">
            <div class="card-header content-card-header">
                <h5 class="content-card-title mb-0">Ringkasan Pinjaman</h5>
            </div>
            <div class="card-body">
                <dl class="row detail-list mb-0">
                    <dt class="col-sm-5">Tanggal</dt>
                    <dd class="col-sm-7">{{ $loan->loan_date->format('d/m/Y') }}</dd>
                    <dt class="col-sm-5">Nominal Pinjaman</dt>
                    <dd class="col-sm-7">{{ format_rupiah($loan->principal_amount) }}</dd>
                    <dt class="col-sm-5">Cicilan/Bulan</dt>
                    <dd class="col-sm-7">{{ format_rupiah($loan->installment_amount) }}</dd>
                    <dt class="col-sm-5">Total Cicilan</dt>
                    <dd class="col-sm-7">{{ $loan->total_installments }} kali</dd>
                    <dt class="col-sm-5">Terbayar</dt>
                    <dd class="col-sm-7">{{ format_rupiah($loan->paid_amount) }}</dd>
                    <dt class="col-sm-5">Sisa</dt>
                    <dd class="col-sm-7"><strong>{{ format_rupiah($loan->remaining_amount) }}</strong></dd>
                    <dt class="col-sm-5">Status</dt>
                    <dd class="col-sm-7">
                        @php
                            $pillColors = ['active' => 'warning', 'paid' => 'success', 'cancelled' => 'secondary'];
                        @endphp
                        <span class="badge badge-pill badge-pill--{{ $pillColors[$loan->status] ?? 'secondary' }}">{{ loan_status_label($loan->status) }}</span>
                    </dd>
                    @if ($loan->notes)
                        <dt class="col-sm-5">Catatan</dt>
                        <dd class="col-sm-7">{{ $loan->notes }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card card-modern content-card">
    <div class="card-header content-card-header">
        <h5 class="content-card-title mb-0">Jadwal Cicilan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jatuh Tempo</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Dibayar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loan->installments as $installment)
                        <tr>
                            <td>{{ $installment->installment_number }}</td>
                            <td>{{ $installment->due_date->format('d/m/Y') }}</td>
                            <td>{{ format_rupiah($installment->amount) }}</td>
                            <td>
                                @php
                                    $iPillColors = ['pending' => 'warning', 'paid' => 'success', 'cancelled' => 'secondary'];
                                @endphp
                                <span class="badge badge-pill badge-pill--{{ $iPillColors[$installment->status] ?? 'secondary' }}">{{ loan_installment_status_label($installment->status) }}</span>
                            </td>
                            <td>
                                @if ($installment->paid_at)
                                    {{ $installment->paid_at->format('d/m/Y H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($installment->status === \App\Models\EmployeeLoanInstallment::STATUS_PENDING && $loan->status === \App\Models\EmployeeLoan::STATUS_ACTIVE)
                                    <form action="{{ route('employee-loan-installments.pay', $installment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Bayar">
                                            <i class="bx bx-check"></i> Bayar
                                        </button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
