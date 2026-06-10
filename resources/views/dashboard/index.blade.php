@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<x-page-header title="Dashboard" :subtitle="'Selamat datang, '.auth()->user()->name" />

<div class="row">
    <div class="col-lg-8 mb-4 order-0">
        <div class="card card-modern">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-2">Sistem {{ tenant_app_name() }}</h5>
                        <p class="mb-4 text-muted">
                            Kelola karyawan, absensi, dan lembur dari menu sidebar.
                        </p>
                        <a href="{{ route('employees.index') }}" class="btn btn-primary">
                            <i class="bx bx-user me-1"></i> Lihat Karyawan
                        </a>
                    </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img src="{{ asset('sneat/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="Welcome" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 order-1">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card card-modern">
                    <div class="card-body">
                        <span class="d-block mb-1 text-muted">Total Karyawan</span>
                        <h3 class="card-title mb-2">{{ $totalEmployees }}</h3>
                        <small class="text-success">{{ $activeEmployees }} aktif</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card card-modern">
                    <div class="card-body">
                        <span class="d-block mb-1 text-muted">Shift Aktif</span>
                        <h3 class="card-title mb-2">{{ $totalShifts }}</h3>
                        <small><a href="{{ route('shifts.index') }}">Kelola shift</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-modern">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-semibold d-block mb-1 text-muted">Gaji Aktif</span>
                        <h3 class="card-title mb-0">{{ $activeSalaries }}</h3>
                    </div>
                    <span class="badge bg-label-success rounded p-2"><i class="bx bx-wallet bx-sm"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card card-modern">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-semibold d-block mb-1 text-muted">Hadir Hari Ini</span>
                        <h3 class="card-title mb-0">{{ $presentToday }}</h3>
                    </div>
                    <span class="badge bg-label-warning rounded p-2"><i class="bx bx-calendar bx-sm"></i></span>
                </div>
                <small class="text-muted">
                    @if (auth()->user()->employee)
                        <a href="{{ route('attendances.check-in') }}">Absen saya</a>
                    @elseif (auth()->user()->isHrUser())
                        <a href="{{ route('attendances.index') }}">Lihat absensi</a>
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
