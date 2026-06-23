@extends('layouts.app')

@section('title', 'Proses Gaji')

@section('content')
@include('partials.delete-modal')

<x-index-page
    table-id="payroll-periods-table"
    table-title="Daftar Periode Gaji"
    title="Proses Gaji"
    subtitle="Kelola periode payroll bulanan"
    :breadcrumbs="[
        ['label' => 'Kompensasi', 'url' => route('payroll-periods.index')],
        ['label' => 'Proses Gaji', 'url' => route('payroll-periods.index')],
    ]"
>
    <x-slot:actions>
        <a href="{{ route('payroll-periods.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Buat Periode
        </a>
    </x-slot:actions>
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
</x-index-page>
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
