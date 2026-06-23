@extends('layouts.app')

@section('title', 'Unit Organisasi')

@section('content')
<x-index-page
    table-id="organizational-units-table"
    table-title="Daftar Unit Organisasi"
    title="Unit Organisasi"
    subtitle="Kelola departemen dan divisi perusahaan"
    :breadcrumbs="[
        ['label' => 'Organisasi', 'url' => route('organization-structure.index')],
        ['label' => 'Unit Organisasi', 'url' => route('organizational-units.index')],
    ]"
>
    <x-slot:actions>
        <a href="{{ route('organization-structure.index') }}" class="btn btn-outline-primary">
            <i class="bx bx-sitemap me-1"></i> Struktur
        </a>
        <a href="{{ route('organizational-units.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i> Tambah Unit
        </a>
    </x-slot:actions>
    <x-slot:stats>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Total Unit" :value="$stats['total']" icon="bx-buildings" icon-variant="primary" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Unit Aktif" :value="$stats['active']" icon="bx-check-circle" icon-variant="success"
                :progress="$stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0" progress-variant="success" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Unit Nonaktif" :value="$stats['inactive']" icon="bx-x-circle" icon-variant="danger" />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Berisi Karyawan" :value="$stats['with_employees']" icon="bx-group" icon-variant="info" />
        </div>
    </x-slot:stats>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Unit Induk</th>
            <th>Karyawan</th>
            <th>Status</th>
            <th class="no-export">Aksi</th>
        </tr>
    </thead>
    <x-slot:footer>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card card-modern card-gradient">
                    <div class="card-body py-4">
                        <h5 class="text-white mb-2">Visualisasi Hierarki</h5>
                        <p class="text-white-50 mb-4">Lihat struktur organisasi dalam tampilan hierarki interaktif.</p>
                        <a href="{{ route('organization-structure.index') }}" class="btn btn-light">
                            <i class="bx bx-sitemap me-1"></i> Buka Visualisasi Hierarki
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:footer>
</x-index-page>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#organizational-units-table', {
        ajax: { url: '{{ route('organizational-units.data') }}' },
        order: [[1, 'asc']],
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'parent_name', name: 'parent.name', defaultContent: '-' },
            { data: 'employees_count', name: 'employees_count', searchable: false },
            { data: 'status_badge', name: 'is_active', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
