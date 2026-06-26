@extends('layouts.app')

@section('title', 'Data Cabang')

@section('content')
@include('partials.crud-open-modal')
<div class="branches-page">
    <x-index-page
        table-id="branches-table"
        table-title="Daftar Cabang"
        title="Data Cabang"
        subtitle="Kelola data operasional kantor cabang perusahaan dengan efisien."
        :breadcrumbs="[
            ['label' => 'Master Data', 'url' => route('employees.index')],
            ['label' => 'Data Cabang', 'url' => route('branches.index')],
        ]"
    >
        <x-slot:actions>
            <button type="button" class="btn btn-primary" data-crud-create="branchFormModal">
                <i class="bx bx-plus me-1"></i> Tambah Cabang
            </button>
        </x-slot:actions>
        <x-slot:stats>
            @php
                $activeRatio = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
            @endphp
            <div class="col-sm-6 col-xl-3">
                <x-stat-card
                    label="Total Cabang"
                    :value="$stats['total']"
                    icon="bx-git-branch"
                    icon-variant="primary"
                    hint="<span class='stat-trend stat-trend--up'>Terdaftar</span>"
                />
            </div>
            <div class="col-sm-6 col-xl-3">
                <x-stat-card
                    label="Cabang Aktif"
                    :value="$stats['active']"
                    icon="bx-check-circle"
                    icon-variant="primary"
                    :hint="'<span class=\'stat-trend stat-trend--neutral\'>'.$activeRatio.'% Rasio</span>'"
                />
            </div>
            <div class="col-sm-6 col-xl-3">
                <x-stat-card
                    label="Cabang Nonaktif"
                    :value="$stats['inactive']"
                    icon="bx-x-circle"
                    icon-variant="danger"
                    :hint="'<span class=\'stat-trend stat-trend--neutral\'>'.($stats['inactive'] === 0 ? 'Stabil' : 'Perlu review').'</span>'"
                />
            </div>
            <div class="col-sm-6 col-xl-3">
                <x-stat-card
                    label="Berisi Karyawan"
                    :value="$stats['with_employees']"
                    icon="bx-group"
                    icon-variant="info"
                    hint="<span class='stat-trend stat-trend--neutral'><i class='bx bx-trending-up'></i> Terisi</span>"
                />
            </div>
        </x-slot:stats>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>Kota/Alamat</th>
                <th>Kantor Pusat</th>
                <th>Status</th>
                <th class="no-export">Aksi</th>
            </tr>
        </thead>
    </x-index-page>
</div>

<x-crud-form-modal
    modal-id="branchFormModal"
    form-id="branch-form"
    route-prefix="branches"
    resource-key="branch"
    :open-modal="$openCrudModal ?? null"
    title-create="Tambah Cabang Baru"
    title-edit="Edit Cabang"
    subtitle-create="Lengkapi informasi untuk mendaftarkan kantor cabang baru."
    submit-create="Simpan Cabang"
>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="branch_name">Nama Cabang</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="branch_name" name="name" value="{{ old('name') }}" placeholder="Contoh: Cabang Bandung" required />
            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label" for="branch_code">Kode Cabang</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" id="branch_code" name="code" value="{{ old('code') }}" placeholder="Contoh: BDG-01" required />
            @error('code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label" for="branch_type">Tipe Cabang</label>
            <select class="form-select @error('is_head_office') is-invalid @enderror" id="branch_type" name="is_head_office">
                <option value="0" @selected(old('is_head_office', '0') == '0')>Cabang</option>
                <option value="1" @selected(old('is_head_office') == '1')>Kantor Pusat</option>
            </select>
            @error('is_head_office')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-8">
            <label class="form-label" for="branch_address">Alamat/Kota</label>
            <textarea class="form-control @error('address') is-invalid @enderror" id="branch_address" name="address" rows="3" placeholder="Masukkan alamat lengkap kantor...">{{ old('address') }}</textarea>
            @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label" for="branch_city">Kota</label>
            <input type="text" class="form-control @error('city') is-invalid @enderror" id="branch_city" name="city" value="{{ old('city') }}" placeholder="Contoh: Jakarta" />
            @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <label class="form-label mt-3" for="branch_phone">Telepon</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="branch_phone" name="phone" value="{{ old('phone') }}" placeholder="Opsional" />
            @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <div class="branch-status-toggle">
                <div>
                    <div class="branch-status-toggle__label">Status Operasional</div>
                    <div class="branch-status-toggle__hint">Tentukan apakah cabang ini langsung aktif.</div>
                </div>
                <div class="form-check form-switch branch-status-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="branch_is_active" name="is_active" value="1" @checked(old('is_active', true)) />
                </div>
            </div>
        </div>
    </div>
</x-crud-form-modal>
@endsection

@push('datatable-scripts')
<script type="module">
    window.initServerDataTable('#branches-table', {
        ajax: { url: '{{ route('branches.data') }}' },
        order: [[1, 'asc']],
        columns: [
            { data: 'code_badge', name: 'code' },
            { data: 'name_display', name: 'name' },
            { data: 'location_display', name: 'city', orderable: true, searchable: true },
            { data: 'head_office_badge', name: 'is_head_office', orderable: true, searchable: false },
            { data: 'status_badge', name: 'is_active', orderable: true, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
        ],
    });
</script>
@endpush
