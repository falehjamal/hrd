@extends('layouts.app')

@section('title', 'Lokasi Kerja')

@section('content')
<x-index-page
    table-id="work-locations-table"
    table-title="Daftar Lokasi Kerja"
    title="Lokasi Kerja"
    subtitle="Titik koordinat untuk absen GPS"
    :breadcrumbs="[
        ['label' => 'Operasional', 'url' => route('attendances.index')],
        ['label' => 'Lokasi Kerja', 'url' => route('work-locations.index')],
    ]"
>
    <x-slot:actions>
        <a href="{{ route('work-locations.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Lokasi
        </a>
    </x-slot:actions>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Cabang</th>
            <th>Koordinat</th>
            <th>Radius</th>
            <th>Default</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#work-locations-table', {
        ajax: { url: '{{ route('work-locations.data') }}' },
        order: [[0, 'asc']],
        columns: [
            { data: 'name', name: 'name' },
            { data: 'branch_name', name: 'branch.name', defaultContent: 'Global' },
            { data: 'coordinates', name: 'latitude', orderable: false, searchable: false },
            { data: 'radius_display', name: 'radius_meters', searchable: false },
            { data: 'default_badge', name: 'is_default', orderable: true, searchable: false },
            { data: 'status_badge', name: 'is_active', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
