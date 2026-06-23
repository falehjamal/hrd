@extends('layouts.platform')

@section('title', 'Tenant Baru')

@section('content')
<x-form-card
    title="Buat Tenant Baru"
    :breadcrumbs="[
        ['label' => 'Daftar Tenant', 'url' => route('platform.tenants.index')],
        ['label' => 'Tenant Baru'],
    ]"
    back-url="{{ route('platform.tenants.index') }}"
>
    <form method="POST" action="{{ route('platform.tenants.store') }}">
        @csrf

        <h6 class="text-muted mb-3">Data Perusahaan</h6>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label" for="id">ID Tenant</label>
                <input type="text" class="form-control @error('id') is-invalid @enderror" id="id" name="id" value="{{ old('id') }}" placeholder="acme" required />
                <small class="text-muted">Huruf kecil, angka, strip. Menjadi nama DB: hrd_tenant_{id}</small>
                @error('id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="slug">Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" required />
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="app_title">Judul Aplikasi (opsional)</label>
                <input type="text" class="form-control @error('app_title') is-invalid @enderror" id="app_title" name="app_title" value="{{ old('app_title') }}" placeholder="Tampil di sidebar" />
                @error('app_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label" for="name">Nama Perusahaan</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required />
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <h6 class="text-muted mb-3">Admin Pertama</h6>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label" for="admin_name">Nama</label>
                <input type="text" class="form-control @error('admin_name') is-invalid @enderror" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required />
                @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="admin_email">Email</label>
                <input type="email" class="form-control @error('admin_email') is-invalid @enderror" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required />
                @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label" for="admin_username">Username (opsional)</label>
                <input type="text" class="form-control @error('admin_username') is-invalid @enderror" id="admin_username" name="admin_username" value="{{ old('admin_username') }}" />
                @error('admin_username')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="admin_password">Password</label>
                <input type="password" class="form-control @error('admin_password') is-invalid @enderror" id="admin_password" name="admin_password" required />
                @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="admin_password_confirmation">Konfirmasi Password</label>
                <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" required />
            </div>
        </div>

        <x-form-actions cancel-url="{{ route('platform.tenants.index') }}" submit-label="Buat Tenant" class="mt-4" />
    </form>
</x-form-card>
@endsection
