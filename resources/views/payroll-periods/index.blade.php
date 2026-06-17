@extends('layouts.app')

@section('title', 'Proses Gaji')

@section('content')
@include('partials.alerts')
@include('partials.delete-modal')

<x-page-header title="Proses Gaji" subtitle="Kelola periode payroll bulanan">
    <x-slot:actions>
        <a href="{{ route('payroll-periods.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Buat Periode
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="payroll-periods-table" title="Daftar Periode Gaji">
    <thead>
        <tr>
            <th>Periode</th>
            <th>Status</th>
            <th>Jumlah Karyawan</th>
            <th>Total Gaji Bersih</th>
            <th>Finalisasi</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#payroll-periods-table', {
        ajax: { url: '{{ route('payroll-periods.data') }}' },
        order: [[0, 'desc']],
        columns: [
            { data: 'period_display', name: 'period_year', orderable: true, searchable: false },
            { data: 'status_badge', name: 'status', orderable: true, searchable: false },
            { data: 'entries_count', name: 'entries_count', orderable: false, searchable: false },
            { data: 'total_net_display', name: 'entries_sum_net_salary', orderable: false, searchable: false },
            { data: 'finalized_display', name: 'finalized_at', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
