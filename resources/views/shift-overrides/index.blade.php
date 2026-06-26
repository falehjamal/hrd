@extends('layouts.app')

@section('title', 'Jadwal Shift')

@section('content')
@include('partials.crud-open-modal')
@include('partials.alerts')

<x-page-header
    title="Jadwal Shift"
    subtitle="Kalender jadwal kerja, libur, cuti, dan override per tanggal"
    :breadcrumbs="[
        ['label' => 'Operasional', 'url' => route('attendances.index')],
        ['label' => 'Jadwal Shift', 'url' => route('shift-overrides.index')],
    ]"
>
    <x-slot:actions>
        <button type="button" class="btn btn-outline-secondary" id="btn-company-holidays">
            <i class="bx bx-calendar-star me-1"></i> Libur Perusahaan
        </button>
        <button type="button" class="btn btn-primary" data-crud-create="shiftOverrideFormModal" id="btn-add-override">
            <i class="bx bx-plus me-1"></i> Tambah Override
        </button>
    </x-slot:actions>
</x-page-header>

<ul class="nav nav-pills shift-cal-tabs mb-3" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-calendar" type="button" role="tab">
            <i class="bx bx-calendar me-1"></i> Kalender
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-list" type="button" role="tab">
            <i class="bx bx-list-ul me-1"></i> Daftar Override
        </button>
    </li>
</ul>

<div class="tab-content p-0 bg-transparent">
    <div class="tab-pane fade show active" id="tab-calendar" role="tabpanel">
        <div class="card card-modern shift-cal-card">
            <div class="card-body">
                <div class="shift-cal-toolbar">
                    <div class="shift-cal-toolbar-filter">
                        <label class="form-label small text-muted mb-1" for="cal-filter-employee">Karyawan</label>
                        <select id="cal-filter-employee" class="form-select" style="width: 100%">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="shift-cal-monthnav">
                        <button type="button" class="btn btn-icon btn-outline-secondary rounded-circle" id="cal-prev-month" aria-label="Bulan sebelumnya">
                            <i class="bx bx-chevron-left"></i>
                        </button>
                        <h5 class="shift-cal-month-label mb-0" id="cal-month-label">—</h5>
                        <button type="button" class="btn btn-icon btn-outline-secondary rounded-circle" id="cal-next-month" aria-label="Bulan berikutnya">
                            <i class="bx bx-chevron-right"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary shift-cal-today-btn" id="cal-today">
                            <i class="bx bx-target-lock me-1"></i> Hari Ini
                        </button>
                    </div>
                </div>

                <div class="shift-cal-legend" id="cal-legend"></div>

                <div class="shift-calendar" id="shift-calendar">
                    <div class="shift-cal-weekdays" id="cal-weekdays"></div>
                    <div class="shift-cal-grid" id="cal-grid">
                        <div class="shift-cal-loading">
                            <span class="spinner-border spinner-border-sm me-2"></span> Memuat kalender…
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-list" role="tabpanel">
        <x-datatable-card tableId="shift-overrides-table" title="Daftar Override">
            <x-slot:headerActions>
                <button type="button" class="btn btn-primary btn-sm" data-crud-create="shiftOverrideFormModal">
                    <i class="bx bx-plus me-1"></i> Tambah Override
                </button>
            </x-slot:headerActions>
            <x-slot:filters>
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label" for="filter-employee">Karyawan</label>
                        <select id="filter-employee" class="form-select" style="width: 100%">
                            <option value=""></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="filter-date-from">Dari Tanggal</label>
                        <input type="date" id="filter-date-from" class="form-control" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="filter-date-to">Sampai Tanggal</label>
                        <input type="date" id="filter-date-to" class="form-control" />
                    </div>
                    <div class="col-md-2">
                        <button type="button" id="btn-apply-shift-filter" class="btn btn-primary">Terapkan</button>
                        <button type="button" id="btn-reset-shift-filter" class="btn btn-outline-secondary">Reset</button>
                    </div>
                </div>
            </x-slot:filters>
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Catatan</th>
                    <th class="no-export">Aksi</th>
                </tr>
            </thead>
        </x-datatable-card>
    </div>
</div>

@include('shift-overrides.calendar-modals')

<x-crud-form-modal
    modal-id="shiftOverrideFormModal"
    form-id="shift-override-form"
    route-prefix="shift-overrides"
    resource-key="shift_override"
    :open-modal="$openCrudModal ?? null"
    title-create="Tambah Override Jadwal"
    title-edit="Edit Override Jadwal"
    subtitle-create="Atur jadwal khusus karyawan pada tanggal tertentu."
    submit-create="Simpan Override"
>
    @include('shift-overrides._form', ['override' => null, 'employees' => $employees, 'shifts' => $shifts])
</x-crud-form-modal>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
@endpush

@push('datatable-scripts')
<script type="module">
    const table = window.initServerDataTable('#shift-overrides-table', {
        ajax: {
            url: '{{ route('shift-overrides.data') }}',
            data: (d) => {
                d.employee_id = document.getElementById('filter-employee')?.value;
                d.date_from = document.getElementById('filter-date-from')?.value;
                d.date_to = document.getElementById('filter-date-to')?.value;
            },
        },
        order: [[1, 'desc']],
        columns: [
            { data: 'employee_display', name: 'employee.name' },
            { data: 'date_display', name: 'date' },
            { data: 'shift_display', name: 'shift_id', searchable: false },
            { data: 'notes', name: 'notes' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
    document.getElementById('btn-apply-shift-filter')?.addEventListener('click', () => table.ajax.reload());
    document.getElementById('btn-reset-shift-filter')?.addEventListener('click', () => {
        const employee = document.getElementById('filter-employee');
        const dateFrom = document.getElementById('filter-date-from');
        const dateTo = document.getElementById('filter-date-to');
        if (employee) employee.value = '';
        if (dateFrom) dateFrom.value = '';
        if (dateTo) dateTo.value = '';
        if (employee && window.jQuery?.fn?.select2) {
            window.jQuery(employee).val(null).trigger('change');
        }
        table.ajax.reload();
    });
</script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    window.shiftCalendarConfig = {
        calendarUrl: @json(route('shift-overrides.calendar')),
        dayDetailUrl: @json(route('shift-overrides.day-detail')),
        showOverrideUrl: @json(url('shift-overrides')),
        overrideModalId: 'shiftOverrideFormModal',
        attendanceUrl: @json(url('attendances')),
        companyHolidaysUrl: @json(route('company-holidays.data')),
        companyHolidayStoreUrl: @json(route('company-holidays.store')),
        companyHolidayUpdateUrl: @json(url('company-holidays')),
        employeeSearchUrl: @json(route('employees.search')),
        csrfToken: @json(csrf_token()),
    };
</script>
<script>
    (function () {
        const form = document.getElementById('shift-override-form');
        if (!form) {
            return;
        }

        const dayOff = form.querySelector('#is_day_off');
        const shiftField = form.querySelector('#shift-field');
        const shiftSelect = form.querySelector('#shift_id');

        const toggleDayOff = () => {
            const off = dayOff?.checked;
            shiftField?.classList.toggle('d-none', off);
            if (off && shiftSelect) {
                shiftSelect.value = '';
            }
        };

        dayOff?.addEventListener('change', toggleDayOff);
        form.addEventListener('crud-form:filled', toggleDayOff);
        form.addEventListener('crud-form:reset', toggleDayOff);
        toggleDayOff();
    })();
</script>
@vite(['resources/js/shift-calendar.js'])
@endpush
