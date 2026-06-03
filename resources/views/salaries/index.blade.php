@extends('layouts.app')

@section('title', 'Master Gaji')

@section('content')
@include('partials.alerts')

<x-page-header title="Master Gaji" subtitle="Daftar gaji karyawan">

</x-page-header>

<x-datatable-card tableId="salaries-table" title="Master Gaji Karyawan">
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="filter-active-only">Tampilan</label>
                <select id="filter-active-only" class="form-select">
                    <option value="1">Gaji aktif saja</option>
                    <option value="0">Semua riwayat</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" id="btn-apply-salary-filter" class="btn btn-primary">Terapkan</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            <th>Karyawan</th>
            <th>ID</th>
            <th>Berlaku</th>
            <th>Gaji Pokok</th>
            <th>Tunjangan</th>
            <th>Total</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    const table = window.initServerDataTable('#salaries-table', {
        ajax: {
            url: '{{ route('salaries.data') }}',
            data: (d) => {
                d.active_only = document.getElementById('filter-active-only')?.value ?? '1';
            },
        },
        order: [[2, 'desc']],
        columns: [
            { data: 'employee_name', name: 'employee.name', orderable: true, searchable: true },
            { data: 'employee_code', name: 'employee.employee_code', orderable: false, searchable: true },
            { data: 'effective_date_display', name: 'effective_date' },
            { data: 'basic_display', name: 'basic_salary', orderable: true, searchable: false },
            { data: 'allowance_display', name: 'fixed_allowance', orderable: false, searchable: false },
            { data: 'total_display', name: 'basic_salary', orderable: false, searchable: false },
            { data: 'status_badge', name: 'is_active', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });

    document.getElementById('btn-apply-salary-filter')?.addEventListener('click', () => table.ajax.reload());
</script>
@endpush
