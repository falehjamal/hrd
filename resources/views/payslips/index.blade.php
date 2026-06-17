@extends('layouts.app')

@section('title', 'Slip Gaji')

@section('content')
@include('partials.alerts')

<x-page-header title="Slip Gaji" subtitle="Riwayat gaji yang sudah difinalisasi">
</x-page-header>

<x-datatable-card tableId="payslips-table" title="Slip Gaji Saya">
    <thead>
        <tr>
            <th>Periode</th>
            <th>Pendapatan</th>
            <th>Potongan</th>
            <th>Gaji Bersih</th>
            <th>Tanggal Final</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#payslips-table', {
        ajax: { url: '{{ route('payslips.data') }}' },
        order: [[0, 'desc']],
        columns: [
            { data: 'period_display', name: 'payroll_periods.period_year', orderable: true, searchable: false },
            { data: 'earnings_display', name: 'total_earnings', orderable: true, searchable: false },
            { data: 'deductions_display', name: 'total_deductions', orderable: true, searchable: false },
            { data: 'net_display', name: 'net_salary', orderable: true, searchable: false },
            { data: 'finalized_display', name: 'payroll_periods.finalized_at', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
