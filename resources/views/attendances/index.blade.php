@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
<x-index-page
    table-id="attendances-table"
    table-title="Daftar Absensi"
    title="Absensi"
    subtitle="Kelola dan koreksi data kehadiran karyawan"
    :breadcrumbs="[
        ['label' => 'Operasional', 'url' => route('attendances.index')],
        ['label' => 'Absensi', 'url' => route('attendances.index')],
    ]"
>
    <x-slot:actions>
        <a href="{{ route('attendances.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Absensi
        </a>
    </x-slot:actions>
    <x-slot:filters>
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label" for="filter-date-from">Dari Tanggal</label>
                <input type="date" id="filter-date-from" class="form-control" />
            </div>
            <div class="col-md-3">
                <label class="form-label" for="filter-date-to">Sampai Tanggal</label>
                <input type="date" id="filter-date-to" class="form-control" />
            </div>
            <div class="col-md-2">
                <label class="form-label" for="filter-status">Status</label>
                <select id="filter-status" class="form-select">
                    <option value="">Semua</option>
                    @foreach (\App\Models\Attendance::statusLabels() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="filter-employee">Karyawan</label>
                <select id="filter-employee" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->employee_code }} — {{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 col-lg-auto">
                <button type="button" id="btn-apply-filter" class="btn btn-primary">Terapkan</button>
                <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary">Reset</button>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            <th>Karyawan</th>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Masuk</th>
            <th>Pulang</th>
            <th>Aktivitas</th>
            <th>Sumber</th>
            <th>Status</th>
            <th>Foto</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    const table = window.initServerDataTable('#attendances-table', {
        ajax: {
            url: '{{ route('attendances.data') }}',
            data: (d) => {
                d.date_from = document.getElementById('filter-date-from')?.value;
                d.date_to = document.getElementById('filter-date-to')?.value;
                d.status = document.getElementById('filter-status')?.value;
                d.employee_id = document.getElementById('filter-employee')?.value;
            },
        },
        order: [[1, 'desc']],
        columns: [
            { data: 'employee_display', name: 'employee.name' },
            { data: 'date_display', name: 'date' },
            { data: 'shift_display', name: 'shift_id', searchable: false },
            { data: 'check_in_display', name: 'check_in_at', searchable: false },
            { data: 'check_out_display', name: 'check_out_at', searchable: false },
            { data: 'activity_notes_display', name: 'activity_notes' },
            { data: 'source_badge', name: 'source', searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'photo_links', name: 'check_in_photo_path', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
    document.getElementById('btn-apply-filter')?.addEventListener('click', () => table.ajax.reload());
    document.getElementById('btn-reset-filter')?.addEventListener('click', () => {
        document.getElementById('filter-date-from').value = '';
        document.getElementById('filter-date-to').value = '';
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-employee').value = '';
        table.ajax.reload();
    });
</script>
@endpush
