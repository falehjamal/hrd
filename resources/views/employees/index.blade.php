@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
@include('partials.alerts')

<x-page-header
    title="Data Karyawan"
    subtitle="Kelola data karyawan perusahaan"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
    ]"
>
    <x-slot:actions>
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Karyawan
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="employees-table" title="Daftar Karyawan" subtitle="Semua karyawan terdaftar di perusahaan">
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="filter-status">Status</label>
                <select id="filter-status" class="form-select">
                    <option value="">Semua</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="button" id="btn-apply-filter" class="btn btn-primary">Terapkan</button>
                <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary">Reset</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Unit</th>
            <th>Jabatan</th>
            <th>Shift</th>
            <th>Gaji Aktif</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    const table = window.initServerDataTable('#employees-table', {
        ajax: {
            url: '{{ route('employees.data') }}',
            data: (d) => {
                d.status = document.getElementById('filter-status')?.value || '';
            },
        },
        order: [[1, 'asc']],
        columns: [
            { data: 'employee_code', name: 'employee_code' },
            { data: 'name_link', name: 'name', orderable: true, searchable: true },
            { data: 'unit_name', name: 'organizationalUnit.name', defaultContent: '-' },
            { data: 'position_name', name: 'position.name', defaultContent: '-' },
            { data: 'shift_code', name: 'shift_id', orderable: false, searchable: false },
            { data: 'salary_display', name: 'salary_display', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });

    document.getElementById('btn-apply-filter')?.addEventListener('click', () => table.ajax.reload());
    document.getElementById('btn-reset-filter')?.addEventListener('click', () => {
        document.getElementById('filter-status').value = '';
        table.ajax.reload();
    });
</script>
@endpush
