@extends('layouts.app')

@section('title', 'Master Pemotongan')

@section('content')
<x-index-page
    table-id="deductions-table"
    table-title="Master Pemotongan Karyawan"
    title="Master Pemotongan"
    subtitle="Daftar pemotongan karyawan"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Master Pemotongan', 'url' => route('deductions.index')],
    ]"
>
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="filter-active-only">Tampilan</label>
                <select id="filter-active-only" class="form-select">
                    <option value="1">Aktif saja</option>
                    <option value="0">Semua riwayat</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" id="btn-apply-deduction-filter" class="btn btn-primary">Terapkan</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            <th>Karyawan</th>
            <th>ID</th>
            <th>Jenis</th>
            <th>Berlaku</th>
            <th>Nominal</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    const table = window.initServerDataTable('#deductions-table', {
        ajax: {
            url: '{{ route('deductions.data') }}',
            data: (d) => {
                d.active_only = document.getElementById('filter-active-only')?.value ?? '1';
            },
        },
        order: [[3, 'desc']],
        columns: [
            { data: 'employee_name', name: 'employee.name' },
            { data: 'employee_code', name: 'employee.employee_code', orderable: false },
            { data: 'type_display', name: 'type_display' },
            { data: 'effective_date_display', name: 'effective_date' },
            { data: 'amount_display', name: 'amount', searchable: false },
            { data: 'status_badge', name: 'is_active', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
    document.getElementById('btn-apply-deduction-filter')?.addEventListener('click', () => table.ajax.reload());
</script>
@endpush
