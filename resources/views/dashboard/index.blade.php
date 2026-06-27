@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@include('partials.alerts')

{{-- Hero Welcome --}}
<div class="dashboard-hero mb-4">
    <div class="dashboard-hero-content">
        <span class="dashboard-hero-badge">{{ tenant_app_name() }}</span>
        <h2 class="dashboard-hero-title">Selamat datang kembali, {{ auth()->user()->name }}!</h2>
        <p class="dashboard-hero-text">
            @if (auth()->user()->isHrUser())
                Anda memiliki {{ $pendingLeaveRequests }} pengajuan cuti dan {{ $pendingOvertimeRequests }} lembur menunggu persetujuan.
            @else
                Kelola absensi dan slip gaji Anda dari menu operasional.
            @endif
        </p>
        <div class="d-flex flex-wrap gap-2 mt-3">
            @if (auth()->user()->isHrUser())
                <a href="{{ route('employees.create') }}" class="btn btn-light btn-hero">
                    <i class="bx bx-user-plus me-1"></i> Karyawan Baru
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-light btn-hero">
                    <i class="bx bx-bar-chart-alt-2 me-1"></i> Laporan Cepat
                </a>
            @else
                <a href="{{ route('attendances.check-in') }}" class="btn btn-light btn-hero">
                    <i class="bx bx-current-location me-1"></i> Absen Sekarang
                </a>
                <a href="{{ route('payslips.index') }}" class="btn btn-outline-light btn-hero">
                    <i class="bx bx-receipt me-1"></i> Slip Gaji
                </a>
            @endif
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            label="Total Karyawan"
            :value="$totalEmployees"
            :hint="'+'.$newEmployeesThisMonth.' bulan ini'"
            icon="bx-group"
            icon-variant="primary"
            :progress="$activeEmployees > 0 ? round(($activeEmployees / max($totalEmployees, 1)) * 100) : 0"
            progress-variant="primary"
        />
    </div>
    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            label="Kehadiran Hari Ini"
            :value="$presentToday"
            :hint="$attendanceRate.'% dari '.$activeEmployees.' karyawan aktif'"
            icon="bx-calendar-check"
            icon-variant="success"
            :progress="$attendanceRate"
            progress-variant="success"
        />
    </div>
    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            label="Shift Aktif"
            :value="$totalShifts"
            hint="Jadwal kerja terkonfigurasi"
            icon="bx-time"
            icon-variant="info"
            :progress="min(100, $totalShifts * 10)"
            progress-variant="info"
        />
    </div>
    <div class="col-sm-6 col-xl-3">
        <x-stat-card
            label="Terlambat Hari Ini"
            :value="$lateToday"
            :hint="$lateToday > 0 ? '<span class=\'text-danger\'>Perlu perhatian</span>' : 'Semua tepat waktu'"
            icon="bx-error-circle"
            icon-variant="danger"
            :progress="$presentToday > 0 ? round(($lateToday / $presentToday) * 100) : 0"
            progress-variant="danger"
        />
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Quick Actions --}}
    <div class="col-lg-5">
        <div class="card card-modern h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="mb-1">Aksi Cepat</h5>
                <p class="text-muted small mb-0">Tugas prioritas hari ini</p>
            </div>
            <div class="card-body pt-3">
                @if (auth()->user()->isHrUser())
                    <a href="{{ route('leave-requests.index') }}" class="quick-action-item">
                        <span class="quick-action-icon quick-action-icon--primary"><i class="bx bx-calendar-check"></i></span>
                        <span class="quick-action-body">
                            <span class="quick-action-title">Setujui Cuti</span>
                            <span class="quick-action-sub">{{ $pendingLeaveRequests }} pengajuan menunggu</span>
                        </span>
                        <i class="bx bx-chevron-right quick-action-arrow"></i>
                    </a>
                    <a href="{{ route('payroll-periods.index') }}" class="quick-action-item">
                        <span class="quick-action-icon quick-action-icon--success"><i class="bx bx-money"></i></span>
                        <span class="quick-action-body">
                            <span class="quick-action-title">Proses Gaji</span>
                            <span class="quick-action-sub">{{ $activeSalaries }} gaji aktif</span>
                        </span>
                        <i class="bx bx-chevron-right quick-action-arrow"></i>
                    </a>
                    <a href="{{ route('overtime-requests.index') }}" class="quick-action-item">
                        <span class="quick-action-icon quick-action-icon--warning"><i class="bx bx-time-five"></i></span>
                        <span class="quick-action-body">
                            <span class="quick-action-title">Alert Lembur</span>
                            <span class="quick-action-sub">{{ $pendingOvertimeRequests }} menunggu review</span>
                        </span>
                        <i class="bx bx-chevron-right quick-action-arrow"></i>
                    </a>
                @else
                    <a href="{{ route('attendances.check-in') }}" class="quick-action-item">
                        <span class="quick-action-icon quick-action-icon--primary"><i class="bx bx-current-location"></i></span>
                        <span class="quick-action-body">
                            <span class="quick-action-title">Absen Masuk</span>
                            <span class="quick-action-sub">Catat kehadiran hari ini</span>
                        </span>
                        <i class="bx bx-chevron-right quick-action-arrow"></i>
                    </a>
                    <a href="{{ route('leave-requests.index') }}" class="quick-action-item">
                        <span class="quick-action-icon quick-action-icon--info"><i class="bx bx-calendar"></i></span>
                        <span class="quick-action-body">
                            <span class="quick-action-title">Ajukan Cuti</span>
                            <span class="quick-action-sub">Kelola permohonan cuti</span>
                        </span>
                        <i class="bx bx-chevron-right quick-action-arrow"></i>
                    </a>
                    <a href="{{ route('payslips.index') }}" class="quick-action-item">
                        <span class="quick-action-icon quick-action-icon--success"><i class="bx bx-receipt"></i></span>
                        <span class="quick-action-body">
                            <span class="quick-action-title">Slip Gaji</span>
                            <span class="quick-action-sub">Lihat riwayat pembayaran</span>
                        </span>
                        <i class="bx bx-chevron-right quick-action-arrow"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Analytics Preview --}}
    <div class="col-lg-7">
        <div class="card card-modern card-gradient h-100">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <h5 class="text-white mb-2">Analitik Tenaga Kerja</h5>
                    <p class="text-white-50 mb-4">Pantau kehadiran, shift, dan komposisi karyawan secara real-time.</p>
                </div>
                <div class="dashboard-analytics-preview">
                    <div class="row g-3 text-white">
                        <div class="col-4">
                            <div class="analytics-mini-stat">
                                <span class="analytics-mini-value">{{ $activeEmployees }}</span>
                                <span class="analytics-mini-label">Aktif</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="analytics-mini-stat">
                                <span class="analytics-mini-value">{{ $presentToday }}</span>
                                <span class="analytics-mini-label">Hadir</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="analytics-mini-stat">
                                <span class="analytics-mini-value">{{ $attendanceRate }}%</span>
                                <span class="analytics-mini-label">Rate</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if (auth()->user()->isHrUser())
                    <a href="{{ route('reports.attendance') }}" class="btn btn-light btn-sm align-self-start mt-4">
                        Lihat Laporan <i class="bx bx-right-arrow-alt ms-1"></i>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Employee Presence Table --}}
@if (auth()->user()->isHrUser())
<div class="card card-modern">
    <div class="card-header datatable-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="datatable-card-title mb-0">Ringkasan Kehadiran Hari Ini</h5>
            <p class="datatable-card-subtitle mb-0">Karyawan yang sudah absen</p>
        </div>
        <a href="{{ route('attendances.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern table-hover mb-0">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Check-in</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($todayAttendances as $attendance)
                        @php
                            $statusClass = match ($attendance->status) {
                                \App\Models\Attendance::STATUS_LATE => 'badge-pill--danger',
                                \App\Models\Attendance::STATUS_PRESENT => 'badge-pill--primary',
                                \App\Models\Attendance::STATUS_LEAVE => 'badge-pill--info',
                                default => 'badge-pill--secondary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-initials">{{ strtoupper(substr($attendance->employee?->name ?? '?', 0, 2)) }}</span>
                                    <div>
                                        <div class="fw-semibold">{{ $attendance->employee?->name ?? '-' }}</div>
                                        <small class="text-muted">{{ $attendance->employee?->employee_code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $attendance->employee?->organizationalUnit?->name ?? '-' }}</td>
                            <td>
                                <span class="badge badge-pill {{ $statusClass }}">
                                    {{ \App\Models\Attendance::statusLabels()[$attendance->status] ?? $attendance->status }}
                                </span>
                            </td>
                            <td class="{{ $attendance->status === \App\Models\Attendance::STATUS_LATE ? 'text-danger fw-medium' : '' }}">
                                {{ $attendance->check_in_at?->format('H:i') ?? '-' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('attendances.edit', $attendance) }}" class="btn btn-sm btn-icon-modern btn-outline-secondary" title="Detail">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data kehadiran hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-center bg-transparent border-0 pt-0 pb-3">
        <a href="{{ route('attendances.index') }}" class="text-primary fw-medium">Lihat Semua Rekaman</a>
    </div>
</div>
@endif
@endsection
