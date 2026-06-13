@extends('layouts.app')

@section('title', 'Piutang Karyawan')

@section('content')
@include('partials.alerts')

<x-page-header title="Piutang Karyawan" subtitle="Kelola kasbon dan cicilan karyawan">
    <x-slot:actions>
        <a href="{{ route('employee-loans.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Catat Piutang
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="employee-loans-table" title="Daftar Piutang">
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="filter-status">Status</label>
                <select id="filter-status" class="form-select">
                    <option value="">Semua</option>
                    @foreach (\App\Models\EmployeeLoan::statusLabels() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" id="btn-apply-loan-filter" class="btn btn-primary">Terapkan</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            <th>Karyawan</th>
            <th>Tanggal</th>
            <th>Pinjaman</th>
            <th>Cicilan/Bulan</th>
            <th>Sisa</th>
            <th>Jadwal</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    const table = window.initServerDataTable('#employee-loans-table', {
        ajax: {
            url: '{{ route('employee-loans.data') }}',
            data: (d) => {
                d.status = document.getElementById('filter-status')?.value;
            },
        },
        order: [[1, 'desc']],
        columns: [
            { data: 'employee_display', name: 'employee.name' },
            { data: 'loan_date_display', name: 'loan_date' },
            { data: 'principal_display', name: 'principal_amount', searchable: false },
            { data: 'installment_display', name: 'installment_amount', searchable: false },
            { data: 'remaining_display', name: 'paid_amount', searchable: false },
            { data: 'progress_display', name: 'total_installments', searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
    document.getElementById('btn-apply-loan-filter')?.addEventListener('click', () => table.ajax.reload());
</script>
@endpush
