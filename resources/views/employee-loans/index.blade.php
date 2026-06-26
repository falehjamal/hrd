@extends('layouts.app')

@section('title', 'Piutang Karyawan')

@section('content')
@include('partials.crud-open-modal')
<x-index-page
    table-id="employee-loans-table"
    table-title="Daftar Piutang"
    title="Piutang Karyawan"
    subtitle="Kelola kasbon dan cicilan karyawan"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Piutang Karyawan', 'url' => route('employee-loans.index')],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-primary" data-crud-create="employeeLoanFormModal">
            <i class="bx bx-plus me-1"></i> Catat Piutang
        </button>
    </x-slot:actions>
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
</x-index-page>

<x-crud-form-modal
    modal-id="employeeLoanFormModal"
    form-id="employee-loan-form"
    route-prefix="employee-loans"
    :open-modal="$openCrudModal ?? null"
    title-create="Catat Piutang"
    subtitle-create="Form piutang / kasbon"
    submit-create="Simpan"
>
    @include('employee-loans._form')
</x-crud-form-modal>
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

    const principalField = document.getElementById('principal_amount');
    const installmentField = document.getElementById('installment_amount');
    const previewText = document.getElementById('preview-text');

    async function updatePreview() {
        const principal = principalField?.value;
        const installment = installmentField?.value;

        if (!principal || !installment) {
            previewText.textContent = 'Isi nominal pinjaman dan cicilan';
            return;
        }

        const params = new URLSearchParams({ principal_amount: principal, installment_amount: installment });
        const response = await fetch(`{{ route('employee-loans.preview') }}?${params.toString()}`);
        const data = await response.json();

        previewText.textContent = `${data.total_installments} cicilan (cicilan terakhir: Rp ${Number(data.last_installment_amount).toLocaleString('id-ID')})`;
    }

    [principalField, installmentField].forEach((el) => el?.addEventListener('input', updatePreview));
    document.getElementById('employeeLoanFormModal')?.addEventListener('shown.bs.modal', () => {
        const employeeId = new URLSearchParams(window.location.search).get('employee_id');
        if (employeeId) {
            document.getElementById('employee_id').value = employeeId;
        }
        updatePreview();
    });
    document.getElementById('employee-loan-form')?.addEventListener('crud-form:reset', () => {
        previewText.textContent = 'Isi nominal pinjaman dan cicilan';
    });
</script>
@endpush
