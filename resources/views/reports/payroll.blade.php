@extends('layouts.app')

@section('title', 'Rekap Payroll')

@section('content')
<x-index-page
    table-id="payroll-report-table"
    table-title="Rekap Gaji per Karyawan"
    title="Rekap Payroll"
    subtitle="Scope: {{ $scopeLabel }}"
    :breadcrumbs="[
        ['label' => 'Laporan', 'url' => route('reports.index')],
        ['label' => 'Rekap Payroll', 'url' => route('reports.payroll')],
    ]"
>
    <x-slot:stats>
        <div class="row g-3 mb-1" id="payroll-summary-cards">
            <div class="col-sm-6 col-xl-3">
                <x-stat-card label="Karyawan Diproses" :value="$summary['processed']" icon="bx-group" icon-variant="primary" />
            </div>
            <div class="col-sm-6 col-xl-3">
                <x-stat-card label="Total Penghasilan" :value="format_rupiah($summary['total_earnings'])" icon="bx-trending-up" icon-variant="success" />
            </div>
            <div class="col-sm-6 col-xl-3">
                <x-stat-card label="Total Potongan" :value="format_rupiah($summary['total_deductions'])" icon="bx-minus-circle" icon-variant="warning" />
            </div>
            <div class="col-sm-6 col-xl-3">
                <x-stat-card label="Total Gaji Bersih" :value="format_rupiah($summary['net_salary'])" icon="bx-wallet" icon-variant="info" />
            </div>
        </div>
    </x-slot:stats>
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label" for="filter-period">Periode Gaji</label>
                <select id="filter-period" class="form-select">
                    @forelse ($periods as $period)
                        <option value="{{ $period->id }}" @selected($periodId === $period->id)>
                            {{ $period->periodLabel() }} (Final)
                        </option>
                    @empty
                        <option value="">Belum ada periode final</option>
                    @endforelse
                </select>
            </div>
            <div class="col-md-12 col-lg-auto">
                <button type="button" id="btn-apply-filter" class="btn btn-primary">Terapkan</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            <th>Karyawan</th>
            <th>Unit</th>
            <th>Penghasilan</th>
            <th>Potongan</th>
            <th>Gaji Bersih</th>
            <th>Status</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    const summaryUrl = @json(route('reports.payroll.summary'));
    const formatRupiah = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);

    const table = window.initServerDataTable('#payroll-report-table', {
        ajax: {
            url: @json(route('reports.payroll.data')),
            data: (d) => {
                d.payroll_period_id = document.getElementById('filter-period')?.value;
            },
        },
        order: [[4, 'desc']],
        columns: [
            { data: 'employee_display', name: 'employee.name' },
            { data: 'unit_display', name: 'unit_display', orderable: false },
            { data: 'earnings_display', name: 'total_earnings', searchable: false },
            { data: 'deductions_display', name: 'total_deductions', searchable: false },
            { data: 'net_display', name: 'net_salary', searchable: false },
            { data: 'status_badge', name: 'is_skipped', searchable: false },
        ],
    });

    async function refreshSummary() {
        const periodId = document.getElementById('filter-period')?.value;
        if (!periodId) return;

        const res = await fetch(`${summaryUrl}?payroll_period_id=${periodId}`);
        const data = await res.json();
        const cards = document.getElementById('payroll-summary-cards');
        const values = [
            data.processed,
            formatRupiah(data.total_earnings),
            formatRupiah(data.total_deductions),
            formatRupiah(data.net_salary),
        ];
        cards?.querySelectorAll('.stat-card-value').forEach((el, i) => {
            if (values[i] !== undefined) el.textContent = values[i];
        });
    }

    document.getElementById('btn-apply-filter')?.addEventListener('click', () => {
        table.ajax.reload();
        refreshSummary();
    });
</script>
@endpush
