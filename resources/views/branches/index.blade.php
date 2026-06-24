@extends('layouts.app')

@section('title', 'Data Cabang')

@section('content')
<x-index-page
    table-id="branches-table"
    table-title="Daftar Cabang"
    title="Data Cabang"
    subtitle="Kelola cabang kantor perusahaan"
    :breadcrumbs="[
        ['label' => 'Master Data', 'url' => route('employees.index')],
        ['label' => 'Data Cabang', 'url' => route('branches.index')],
    ]"
>
    <x-slot:actions>
        <a href="{{ route('branches.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Cabang
        </a>
    </x-slot:actions>
    <x-slot:stats>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Total Cabang" :value="$stats['total']" icon="bx-git-branch" icon-variant="primary" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Cabang Aktif" :value="$stats['active']" icon="bx-check-circle" icon-variant="success"
                :progress="$stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0" progress-variant="success" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Cabang Nonaktif" :value="$stats['inactive']" icon="bx-x-circle" icon-variant="danger" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Berisi Karyawan" :value="$stats['with_employees']" icon="bx-group" icon-variant="info" />
        </div>
    </x-slot:stats>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kota/Alamat</th>
            <th>Karyawan</th>
            <th>Kantor Pusat</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#branches-table', {
        ajax: { url: '{{ route('branches.data') }}' },
        order: [[1, 'asc']],
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'location_display', name: 'city', orderable: true, searchable: true },
            { data: 'employees_count', name: 'employees_count', searchable: false },
            { data: 'head_office_badge', name: 'is_head_office', orderable: true, searchable: false },
            { data: 'status_badge', name: 'is_active', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
