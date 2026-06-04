@extends('layouts.app')

@section('title', 'Absen Saya')

@section('content')
@include('partials.alerts')

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card card-modern">
            <div class="card-body text-center py-4">
                <h5 class="mb-1">{{ $employee->name }}</h5>
                <p class="text-muted mb-3">{{ $employee->employee_code }} · {{ today()->translatedFormat('l, d F Y') }}</p>

                @if ($location)
                    <p class="small text-muted mb-3">
                        <i class="bx bx-map-pin"></i> {{ $location->name }}
                        (radius {{ $location->radius_meters }} m)
                    </p>
                @else
                    <div class="alert alert-warning">Lokasi kerja belum dikonfigurasi. Hubungi HR.</div>
                @endif

                @if ($todayAttendance)
                    <div class="mb-4">
                        @if ($todayAttendance->check_in_at)
                            <span class="badge bg-label-success me-1">Masuk {{ $todayAttendance->check_in_at->format('H:i') }}</span>
                        @endif
                        @if ($todayAttendance->check_out_at)
                            <span class="badge bg-label-primary">Pulang {{ $todayAttendance->check_out_at->format('H:i') }}</span>
                        @endif
                    </div>
                @endif

                @if ($location && !($todayAttendance?->check_in_at && $todayAttendance?->check_out_at))
                    <div id="absen-ready-banner" class="alert alert-warning text-start small mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        Pertama kali: pilih <strong>Izinkan</strong> untuk kamera & lokasi.
                        Jika browser menawarkan <strong>Selalu izinkan</strong>, aktifkan agar tidak diminta lagi setiap buka halaman ini.
                    </div>

                    <form id="check-in-form" action="{{ route('attendances.check-in.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" />
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" />
                        <input type="hidden" name="action" id="action" value="{{ $todayAttendance?->check_in_at && !$todayAttendance?->check_out_at ? 'check_out' : 'check_in' }}" />

                        <x-attendance-camera
                            id="photo"
                            name="photo"
                            label="Foto Bukti"
                            :required="true"
                            :auto-start="true"
                        />

                        <p id="gps-status" class="small text-muted mb-3">Meminta izin lokasi...</p>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="submit-btn" disabled>
                            @if ($todayAttendance?->check_in_at && !$todayAttendance?->check_out_at)
                                <i class="bx bx-log-out me-1"></i> Absen Pulang
                            @else
                                <i class="bx bx-log-in me-1"></i> Absen Masuk
                            @endif
                        </button>
                    </form>
                @elseif ($todayAttendance?->check_out_at)
                    <p class="text-success mb-0"><i class="bx bx-check-circle"></i> Absensi hari ini sudah lengkap.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/attendance-check-in.js'])
@endpush
