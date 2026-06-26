@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
@include('partials.crud-open-modal')
<x-index-page
    table-id="employees-table"
    table-title="Daftar Karyawan"
    title="Data Karyawan"
    subtitle="Kelola data karyawan perusahaan"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-primary" data-crud-create="employeeFormModal">
            <i class="bx bx-plus me-1"></i> Tambah Karyawan
        </button>
    </x-slot:actions>
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
            @if ($branches->isNotEmpty())
            <div class="col-md-4">
                <label class="form-label" for="filter-branch">Cabang</label>
                <select id="filter-branch" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
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
            @if ($branches->isNotEmpty())
            <th>Cabang</th>
            @endif
            <th>Jabatan</th>
            <th>Shift</th>
            <th>Gaji Aktif</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>

<x-crud-form-modal
    modal-id="employeeFormModal"
    form-id="employee-form"
    route-prefix="employees"
    resource-key="employee"
    :open-modal="$openCrudModal ?? null"
    size="xl"
    enctype="multipart/form-data"
    title-create="Tambah Karyawan"
    title-edit="Edit Karyawan"
    subtitle-create="Lengkapi data karyawan dan akun login."
    submit-create="Simpan Karyawan"
    submit-edit="Simpan Perubahan"
>
    @include('employees._form')
</x-crud-form-modal>
@endsection

@push('datatable-scripts')
<script type="module">
    const table = window.initServerDataTable('#employees-table', {
        ajax: {
            url: '{{ route('employees.data') }}',
            data: (d) => {
                d.status = document.getElementById('filter-status')?.value || '';
                d.branch_id = document.getElementById('filter-branch')?.value || '';
            },
        },
        order: [[1, 'asc']],
        columns: [
            { data: 'employee_code', name: 'employee_code' },
            { data: 'name_link', name: 'name', orderable: true, searchable: true },
            { data: 'unit_name', name: 'organizationalUnit.name', defaultContent: '-' },
            @if ($branches->isNotEmpty())
            { data: 'branch_name', name: 'branch.name', defaultContent: '-' },
            @endif
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
        const branchFilter = document.getElementById('filter-branch');
        if (branchFilter) {
            branchFilter.value = '';
        }
        table.ajax.reload();
    });

    const employeeModal = document.getElementById('employeeFormModal');
    const employeeForm = document.getElementById('employee-form');

    employeeForm?.addEventListener('crud-form:filled', (event) => {
        const record = event.detail?.record;
        const preview = document.getElementById('photo-preview');
        const wrap = document.getElementById('photo-preview-wrap');

        if (record?.photo_url && preview) {
            preview.src = record.photo_url;
            wrap?.classList.remove('d-none');
        }
    });

    employeeForm?.addEventListener('crud-form:reset', () => {
        const preview = document.getElementById('photo-preview');
        const wrap = document.getElementById('photo-preview-wrap');

        if (preview) {
            preview.src = '';
        }
        wrap?.classList.add('d-none');
    });
</script>
@endpush
