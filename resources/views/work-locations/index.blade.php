@extends('layouts.app')

@section('title', 'Lokasi Kerja')

@section('content')
@include('partials.crud-open-modal')
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
        <button type="button" class="btn btn-primary" data-crud-create="workLocationFormModal">
            <i class="bx bx-plus me-1"></i> Tambah Lokasi
        </button>
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

<x-crud-form-modal
    modal-id="workLocationFormModal"
    form-id="work-location-form"
    route-prefix="work-locations"
    resource-key="work_location"
    :open-modal="$openCrudModal ?? null"
    title-create="Tambah Lokasi Kerja"
    title-edit="Edit Lokasi Kerja"
    subtitle-create="Lengkapi koordinat GPS dan radius geofence."
    submit-create="Simpan Lokasi"
>
    @include('work-locations._form', ['workLocation' => null, 'branches' => $branches])
</x-crud-form-modal>
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
@vite(['resources/js/work-location-gps.js'])
@endpush
