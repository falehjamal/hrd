@extends('layouts.app')

@section('title', 'Detail Periode Gaji')

@section('content')
@include('partials.alerts')
@include('partials.delete-modal')

<x-page-header
    title="Periode {{ $period->periodLabel() }}"
    subtitle="Proses gaji bulanan"
    :breadcrumbs="[
        ['label' => 'Periode Gaji', 'url' => route('payroll-periods.index')],
        ['label' => $period->periodLabel(), 'url' => route('payroll-periods.show', $period)],
    ]"
>
    <x-slot:actions>
        @if ($period->isDraft())
            <form action="{{ route('payroll-periods.regenerate', $period) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-primary">Hitung Ulang</button>
            </form>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#finalizeModal">
                Finalisasi
            </button>
            <button type="button" class="btn btn-outline-danger"
                data-delete-url="{{ route('payroll-periods.destroy', $period) }}"
                data-delete-message="Hapus periode draft {{ $period->periodLabel() }}?">
                Hapus
            </button>
        @endif
        <a href="{{ route('payroll-periods.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </a>
    </x-slot:actions>
</x-page-header>

<div class="card card-modern content-card mb-4">
    <div class="card-body pb-0">
        <ul class="nav nav-tabs nav-tabs-modern" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-summary" data-bs-toggle="tab" data-bs-target="#panel-summary" type="button" role="tab">
                    <i class="bx bx-bar-chart-alt-2 me-1"></i> Ringkasan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-entries" data-bs-toggle="tab" data-bs-target="#panel-entries" type="button" role="tab">
                    <i class="bx bx-group me-1"></i> Rincian Karyawan
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body pt-0">
        <div class="tab-content tab-content-modern">
            <div class="tab-pane fade show active" id="panel-summary" role="tabpanel">
                <div class="row g-4 mb-0">
                    <div class="col-md-3">
                        <div class="card card-modern h-100">
                            <div class="card-body">
                                <p class="text-muted mb-1">Status</p>
                                @if ($period->isFinalized())
                                    <span class="badge badge-pill badge-pill--success">{{ payroll_period_status_label($period->status) }}</span>
                                @else
                                    <span class="badge badge-pill badge-pill--warning">{{ payroll_period_status_label($period->status) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <x-stat-card label="Diproses" :value="$summary['processed']" icon="bx-user-check" icon-variant="primary" />
                    </div>
                    <div class="col-md-3">
                        <x-stat-card label="Dilewati" :value="$summary['skipped']" icon="bx-user-x" icon-variant="secondary" />
                    </div>
                    <div class="col-md-3">
                        <x-stat-card label="Total Gaji Bersih" :value="format_rupiah($summary['total_net'])" icon="bx-wallet" icon-variant="success" />
                    </div>
                </div>

                @if ($period->notes)
                    <div class="alert alert-secondary mt-4 mb-0">{{ $period->notes }}</div>
                @endif
            </div>

            <div class="tab-pane fade" id="panel-entries" role="tabpanel">
                <div class="table-responsive">
                    <table id="payroll-entries-table" class="table table-modern table-hover w-100">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                <th>ID</th>
                                <th>Pendapatan</th>
                                <th>Potongan</th>
                                <th>Gaji Bersih</th>
                                <th>Status</th>
                                <th class="no-export">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="finalizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Finalisasi Periode Gaji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Finalisasi periode <strong>{{ $period->periodLabel() }}</strong>? Cicilan piutang akan ditandai lunas dan lembur terproses. Periode tidak dapat diubah setelah difinalisasi.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('payroll-periods.finalize', $period) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">Finalisasi</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('datatable-scripts')
<script type="module">
    const payrollEntriesTable = window.initServerDataTable('#payroll-entries-table', {
        ajax: { url: '{{ route('payroll-periods.entries.data', $period) }}' },
        order: [[0, 'asc']],
        columns: [
            { data: 'employee_name', name: 'employee.name', orderable: true, searchable: true },
            { data: 'employee_code', name: 'employee.employee_code', orderable: false, searchable: true },
            { data: 'earnings_display', name: 'total_earnings', orderable: true, searchable: false },
            { data: 'deductions_display', name: 'total_deductions', orderable: true, searchable: false },
            { data: 'net_display', name: 'net_salary', orderable: true, searchable: false },
            { data: 'status_badge', name: 'is_skipped', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });

    document.getElementById('tab-entries')?.addEventListener('shown.bs.tab', () => {
        payrollEntriesTable.columns.adjust().draw(false);
    });
</script>
@endpush
