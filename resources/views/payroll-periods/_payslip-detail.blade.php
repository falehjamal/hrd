@php
    $earnings = $entry->items->where('type', \App\Models\PayrollEntryItem::TYPE_EARNING);
    $deductions = $entry->items->where('type', \App\Models\PayrollEntryItem::TYPE_DEDUCTION);
@endphp

<div class="card card-modern mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Slip Gaji — {{ $period->periodLabel() }}</h5>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Karyawan</dt>
                    <dd class="col-sm-8">{{ $entry->employee->name }}</dd>
                    <dt class="col-sm-4">ID Karyawan</dt>
                    <dd class="col-sm-8">{{ $entry->employee->employee_code }}</dd>
                    @if ($entry->employee->position)
                        <dt class="col-sm-4">Jabatan</dt>
                        <dd class="col-sm-8">{{ $entry->employee->position->name }}</dd>
                    @endif
                    <dt class="col-sm-4">Status Periode</dt>
                    <dd class="col-sm-8">{{ payroll_period_status_label($period->status) }}</dd>
                </dl>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-muted mb-1">{{ tenant_app_name() }}</p>
                @if ($period->isFinalized())
                    <p class="small text-muted mb-0">Difinalisasi: {{ $period->finalized_at?->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card card-modern h-100">
            <div class="card-header"><h5 class="mb-0">Pendapatan</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <tbody>
                            @forelse ($earnings as $item)
                                <tr>
                                    <td>{{ $item->label }}</td>
                                    <td class="text-end">{{ format_rupiah($item->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">Tidak ada pendapatan.</td></tr>
                            @endforelse
                            <tr class="table-light fw-semibold">
                                <td>Total Pendapatan</td>
                                <td class="text-end">{{ format_rupiah($entry->total_earnings) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-modern h-100">
            <div class="card-header"><h5 class="mb-0">Potongan</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <tbody>
                            @forelse ($deductions as $item)
                                <tr>
                                    <td>{{ $item->label }}</td>
                                    <td class="text-end">{{ format_rupiah($item->amount) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted">Tidak ada potongan.</td></tr>
                            @endforelse
                            <tr class="table-light fw-semibold">
                                <td>Total Potongan</td>
                                <td class="text-end">{{ format_rupiah($entry->total_deductions) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-modern mt-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <span class="fs-5 fw-semibold">Gaji Bersih (Take Home Pay)</span>
        <span class="fs-4 fw-bold text-primary">{{ format_rupiah($entry->net_salary) }}</span>
    </div>
</div>
