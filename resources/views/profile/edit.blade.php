@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
@include('partials.alerts')

@if (session('status') === 'profile-updated')
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bx bx-check-circle me-2"></i>Profil berhasil diperbarui. Notifikasi telah dikirim.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('status') === 'password-updated')
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bx bx-check-circle me-2"></i>Password berhasil diperbarui. Notifikasi telah dikirim.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

<x-page-header
    title="Profil Saya"
    subtitle="Kelola informasi akun dan keamanan login"
    :breadcrumbs="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Profil Saya', 'url' => route('profile.edit')],
    ]"
>
</x-page-header>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-modern content-card">
            <div class="card-header content-card-header">
                <h5 class="content-card-title mb-0">Informasi Akun</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" class="row g-3">
                    @csrf
                    @method('PATCH')

                    <div class="col-md-6">
                        <label class="form-label" for="name">Nama</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" />
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}" required autocomplete="username" />
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" />
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @if ($user->employee)
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->employee->phone) }}" autocomplete="tel" />
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">ID Karyawan</label>
                            <input type="text" class="form-control" value="{{ $user->employee->employee_code }}" disabled />
                            <div class="form-text">ID karyawan hanya dapat diubah oleh HR.</div>
                        </div>
                    @endif

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan Profil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-modern content-card">
            <div class="card-header content-card-header">
                <h5 class="content-card-title mb-0">Ubah Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('password.update') }}" method="POST" class="row g-3">
                    @csrf
                    @method('PUT')

                    <div class="col-12">
                        <label class="form-label" for="current_password">Password Saat Ini</label>
                        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" autocomplete="current-password" />
                        @error('current_password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="password">Password Baru</label>
                        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" autocomplete="new-password" />
                        @error('password', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" id="password_confirmation" name="password_confirmation" autocomplete="new-password" />
                        @error('password_confirmation', 'updatePassword')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-outline-primary"><i class="bx bx-lock-alt me-1"></i> Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
