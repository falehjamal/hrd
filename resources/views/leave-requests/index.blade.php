@extends('layouts.app')

@section('title', 'Cuti')

@section('content')
<x-index-page
    table-id="leave-requests-table"
    table-title="Daftar Pengajuan Cuti"
    title="Cuti"
    subtitle="{{ $isEmployee ? 'Pengajuan cuti Anda' : 'Kelola persetujuan cuti karyawan' }}"
    :breadcrumbs="[
        ['label' => 'Operasional', 'url' => route('attendances.index')],
        ['label' => 'Cuti', 'url' => route('leave-requests.index')],
    ]"
>
    <x-slot:actions>
        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Ajukan Cuti
        </a>
    </x-slot:actions>
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
                <label class="form-label small text-muted mb-1">Jenis Cuti</label>
                <select id="filter-leave-type" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach ($leaveTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->code }} — {{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Status</label>
                <select id="filter-status" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach (\App\Models\LeaveRequest::statusLabels() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-slot:filters>
    <thead>
        <tr>
            @if (!$isEmployee)<th>Karyawan</th>@endif
            <th>Jenis Cuti</th>
            <th>Rentang</th>
            <th>Total Hari</th>
            <th>Alasan</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    const columns = [
        @if (! $isEmployee)
        { data: 'employee_display', name: 'employee.name' },
        @endif
        { data: 'leave_type_display', name: 'leave_type_display' },
        { data: 'date_range', name: 'start_date' },
        { data: 'total_days_display', name: 'total_days', searchable: false },
        { data: 'reason', name: 'reason' },
        { data: 'status_badge', name: 'status', searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
    ];
    window.initServerDataTable('#leave-requests-table', {
        ajax: {
            url: '{{ route('leave-requests.data') }}',
            data: (d) => {
                d.date_from = document.getElementById('filter-date-from')?.value;
                d.date_to = document.getElementById('filter-date-to')?.value;
                d.status = document.getElementById('filter-status')?.value;
                d.leave_type_id = document.getElementById('filter-leave-type')?.value;
            },
        },
        order: [[{{ $isEmployee ? 2 : 3 }}, 'desc']],
        columns,
    });
    ['filter-date-from', 'filter-date-to', 'filter-status', 'filter-leave-type'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', () => {
            window.jQuery('#leave-requests-table').DataTable().ajax.reload();
        });
    });
</script>
@endpush
