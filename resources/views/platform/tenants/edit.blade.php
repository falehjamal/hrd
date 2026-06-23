@extends('layouts.platform')

@section('title', 'Edit '.$tenant->displayName())

@section('content')
<x-form-card
    title="Edit Tenant"
    subtitle="{{ $tenant->displayName() }}"
    :breadcrumbs="[
        ['label' => 'Daftar Tenant', 'url' => route('platform.tenants.index')],
        ['label' => $tenant->displayName(), 'url' => route('platform.tenants.show', $tenant)],
        ['label' => 'Edit Tenant'],
    ]"
    back-url="{{ route('platform.tenants.show', $tenant) }}"
>
    <form method="POST" action="{{ route('platform.tenants.update', $tenant) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">ID Tenant</label>
            <input type="text" class="form-control" value="{{ $tenant->id }}" disabled />
        </div>
        <div class="mb-3">
            <label class="form-label" for="name">Nama Perusahaan</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tenant->name) }}" required />
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="slug">Slug</label>
            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $tenant->slug) }}" required />
            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="app_title">Judul Aplikasi (sidebar)</label>
            <input type="text" class="form-control @error('app_title') is-invalid @enderror" id="app_title" name="app_title" value="{{ old('app_title', $tenant->app_title) }}" placeholder="Kosongkan = pakai nama perusahaan" />
            @error('app_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <x-form-actions cancel-url="{{ route('platform.tenants.show', $tenant) }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
