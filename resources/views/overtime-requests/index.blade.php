@extends('layouts.app')

@section('title', 'Lembur')

@section('content')
@include('partials.alerts')

<x-page-header title="Lembur" subtitle="{{ $isEmployee ? 'Pengajuan lembur Anda' : 'Kelola persetujuan lembur karyawan' }}">
    <x-slot:actions>
        <a href="{{ route('overtime-requests.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Ajukan Lembur
        </a>
    </x-slot:actions>
</x-page-header>

<x-datatable-card tableId="overtime-table" title="Daftar Pengajuan Lembur">
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
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Status</label>
                <select id="filter-status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach (\App\Models\OvertimeRequest::statusLabels() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            @if (!$isEmployee)<th>Karyawan</th>@endif
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Durasi</th>
            <th>Alasan</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-datatable-card>
@endsection

@push('datatable-scripts')
<script type="module">
    const columns = [
        @if (! $isEmployee)
        { data: 'employee_display', name: 'employee.name' },
        @endif
        { data: 'date_display', name: 'date' },
        { data: 'time_range', name: 'start_time', orderable: false, searchable: false },
        { data: 'duration_display', name: 'duration_minutes', searchable: false },
        { data: 'reason', name: 'reason' },
        { data: 'status_badge', name: 'status', searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
    ];
    window.initServerDataTable('#overtime-table', {
        ajax: {
            url: '{{ route('overtime-requests.data') }}',
            data: (d) => {
                d.date_from = document.getElementById('filter-date-from')?.value;
                d.date_to = document.getElementById('filter-date-to')?.value;
                d.status = document.getElementById('filter-status')?.value;
            },
        },
        order: [[{{ $isEmployee ? 0 : 1 }}, 'desc']],
        columns,
    });
    ['filter-date-from', 'filter-date-to', 'filter-status'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', () => {
            window.jQuery('#overtime-table').DataTable().ajax.reload();
        });
    });
</script>
@endpush
