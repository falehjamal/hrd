@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
@include('partials.alerts')

<x-page-header title="Absensi" subtitle="Kelola dan koreksi data kehadiran karyawan">
    <x-slot:actions>
        <a href="{{ route('attendances.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Absensi
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="attendances-table" title="Daftar Absensi">
    <x-slot:filters>
        <div class="filter-toolbar row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Dari Tanggal</label>
                <input type="date" id="filter-date-from" class="form-control form-control-sm" />
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Sampai Tanggal</label>
                <input type="date" id="filter-date-to" class="form-control form-control-sm" />
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted mb-1">Status</label>
                <select id="filter-status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach (\App\Models\Attendance::statusLabels() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted mb-1">Karyawan</label>
                <select id="filter-employee" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->employee_code }} — {{ $emp->name }}</option>
                    @endforeach
                </select>
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
            <th>Sumber</th>
            <th>Status</th>
            <th>Foto</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#attendances-table', {
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
            { data: 'source_badge', name: 'source', searchable: false },
            { data: 'status_badge', name: 'status', searchable: false },
            { data: 'photo_links', name: 'check_in_photo_path', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
    ['filter-date-from', 'filter-date-to', 'filter-status', 'filter-employee'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', () => {
            window.jQuery('#attendances-table').DataTable().ajax.reload();
        });
    });
</script>
@endpush
