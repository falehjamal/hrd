@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-8 mb-4 order-0">
        <div class="card">
            <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Selamat datang, {{ auth()->user()->name }}!</h5>
                        <p class="mb-4">
                            Anda telah berhasil masuk ke sistem <strong>{{ tenant_app_name() }}</strong>.
                            Kelola data karyawan, shift, dan gaji dari menu Master Data.
                        </p>
                        <a href="{{ route('employees.index') }}" class="btn btn-sm btn-primary">Lihat Karyawan</a>
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
                <div class="card">
                    <div class="card-body">
                        <span class="d-block mb-1">Total Karyawan</span>
                        <h3 class="card-title mb-2">{{ $totalEmployees }}</h3>
                        <small class="text-muted">{{ $activeEmployees }} aktif</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <span class="d-block mb-1">Shift Aktif</span>
                        <h3 class="card-title mb-2">{{ $totalShifts }}</h3>
                        <small class="text-muted"><a href="{{ route('shifts.index') }}">Kelola shift</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-semibold d-block mb-1">Gaji Aktif</span>
                        <h3 class="card-title mb-0">{{ $activeSalaries }}</h3>
                    </div>
                    <span class="badge bg-label-success rounded p-2"><i class="bx bx-wallet bx-sm"></i></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-semibold d-block mb-1">Hadir Hari Ini</span>
                        <h3 class="card-title mb-0">—</h3>
                    </div>
                    <span class="badge bg-label-warning rounded p-2"><i class="bx bx-calendar bx-sm"></i></span>
                </div>
                <small class="text-muted">Modul absensi belum tersedia</small>
            </div>
        </div>
    </div>
</div>
@endsection
