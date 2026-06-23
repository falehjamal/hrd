@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<x-form-card
    title="Pengaturan Sistem"
    subtitle="Konfigurasi email, WhatsApp, dan payroll"
    :breadcrumbs="[
        ['label' => 'Pengaturan', 'url' => route('settings.edit')],
    ]"
>
    <ul class="nav nav-tabs nav-tabs-modern mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-email" data-bs-toggle="tab" data-bs-target="#panel-email" type="button" role="tab">
                <i class="bx bx-envelope me-1"></i> Email SMTP
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-wa" data-bs-toggle="tab" data-bs-target="#panel-wa" type="button" role="tab">
                <i class="bx bxl-whatsapp me-1"></i> WhatsApp
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-payroll" data-bs-toggle="tab" data-bs-target="#panel-payroll" type="button" role="tab">
                <i class="bx bx-money me-1"></i> Payroll
            </button>
        </li>
    </ul>

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="tab-content tab-content-modern">
            <div class="tab-pane fade show active" id="panel-email" role="tabpanel">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="mail_enabled" name="mail_enabled" value="1" @checked(old('mail_enabled', $settings['mail_enabled']) == '1')>
                            <label class="form-check-label" for="mail_enabled">Aktifkan notifikasi email</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="mail_host">SMTP Host</label>
                        <input type="text" class="form-control @error('mail_host') is-invalid @enderror" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}" placeholder="smtp.gmail.com">
                        @error('mail_host')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="mail_port">Port</label>
                        <input type="number" class="form-control @error('mail_port') is-invalid @enderror" id="mail_port" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}">
                        @error('mail_port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="mail_encryption">Enkripsi</label>
                        <select class="form-select @error('mail_encryption') is-invalid @enderror" id="mail_encryption" name="mail_encryption">
                            <option value="tls" @selected(old('mail_encryption', $settings['mail_encryption']) === 'tls')>TLS</option>
                            <option value="ssl" @selected(old('mail_encryption', $settings['mail_encryption']) === 'ssl')>SSL</option>
                            <option value="" @selected(old('mail_encryption', $settings['mail_encryption']) === '')>Tanpa</option>
                        </select>
                        @error('mail_encryption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="mail_username">Email Gmail</label>
                        <input type="email" class="form-control @error('mail_username') is-invalid @enderror" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}" placeholder="nama@gmail.com">
                        @error('mail_username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="mail_password">App Password Gmail</label>
                        <input type="password" class="form-control @error('mail_password') is-invalid @enderror" id="mail_password" name="mail_password" placeholder="{{ filled($settings['mail_password']) ? '•••••••• (kosongkan jika tidak diubah)' : 'Masukkan App Password' }}">
                        @error('mail_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Gunakan App Password dari Google (akun Gmail harus aktif 2FA). Kosongkan jika tidak ingin mengubah password yang sudah tersimpan.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="mail_from_address">Alamat Pengirim</label>
                        <input type="email" class="form-control @error('mail_from_address') is-invalid @enderror" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}" placeholder="Sama dengan email Gmail">
                        @error('mail_from_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="mail_from_name">Nama Pengirim</label>
                        <input type="text" class="form-control @error('mail_from_name') is-invalid @enderror" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? tenant_app_name()) }}">
                        @error('mail_from_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="panel-wa" role="tabpanel">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="wa_enabled" name="wa_enabled" value="1" @checked(old('wa_enabled', $settings['wa_enabled']) == '1')>
                            <label class="form-check-label" for="wa_enabled">Aktifkan notifikasi WhatsApp</label>
                        </div>
                        <div class="form-text">Notifikasi WA hanya dikirim jika fitur ini aktif dan nomor WhatsApp tenant sudah terhubung.</div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="panel-payroll" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="payroll_overtime_hourly_rate">Tarif Lembur per Jam (Rp)</label>
                        <input type="number" class="form-control @error('payroll_overtime_hourly_rate') is-invalid @enderror" id="payroll_overtime_hourly_rate" name="payroll_overtime_hourly_rate" value="{{ old('payroll_overtime_hourly_rate', $settings['payroll_overtime_hourly_rate'] ?? 50000) }}" min="0" step="1">
                        @error('payroll_overtime_hourly_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Digunakan saat menghitung upah lembur disetujui dalam proses gaji.</div>
                    </div>
                </div>
            </div>
        </div>

        <x-form-actions :cancel-url="route('dashboard')" cancel-label="Batal" />
    </form>
</x-form-card>

<div class="card card-modern content-card mt-4" id="wa-scan-panel"
    data-connect-url="{{ route('settings.wa.connect') }}"
    data-status-url="{{ route('settings.wa.status') }}"
    data-disconnect-url="{{ route('settings.wa.disconnect') }}">
    <div class="card-header content-card-header d-flex align-items-center justify-content-between">
        <h5 class="content-card-title mb-0"><i class="bx bxl-whatsapp me-1"></i> Koneksi WhatsApp</h5>
        <span id="wa-status-badge" class="badge badge-pill badge-pill--secondary">Memuat...</span>
    </div>
    <div class="card-body">
        @unless ($waGatewayConfigured)
            <div class="alert alert-warning mb-0">
                <i class="bx bx-error-circle me-1"></i>
                Gateway WhatsApp belum dikonfigurasi di server. Hubungi administrator untuk mengisi <code>WA_GATEWAY_URL</code> dan <code>WA_GATEWAY_KEY</code> di file <code>.env</code>.
            </div>
        @else
            <div id="wa-alert" class="alert alert-danger mb-3 d-none" role="alert"></div>

            <div class="row g-4 align-items-start">
                <div class="col-lg-7">
                    <p class="text-muted mb-2">Setiap tenant memiliki sesi WhatsApp terpisah. Scan QR code dengan aplikasi WhatsApp di ponsel untuk menghubungkan nomor pengirim notifikasi.</p>

                    <div class="mb-3">
                        <span class="text-muted">Status:</span>
                        <strong id="wa-status-label">Memuat...</strong>
                    </div>

                    <div class="mb-3">
                        <span class="text-muted">Nomor terhubung:</span>
                        <strong id="wa-phone-display" class="{{ filled($settings['wa_sender']) ? '' : 'd-none' }}">{{ $settings['wa_sender'] }}</strong>
                        <span class="text-muted {{ filled($settings['wa_sender']) ? 'd-none' : '' }}" id="wa-phone-empty">Belum terhubung</span>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-primary" id="wa-btn-connect">
                            <i class="bx bx-qr-scan me-1"></i> Hubungkan / Scan Ulang
                        </button>
                        <button type="button" class="btn btn-outline-danger d-none" id="wa-btn-disconnect">
                            <i class="bx bx-unlink me-1"></i> Putuskan
                        </button>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div id="wa-qr-container" class="border rounded p-3 text-center d-none">
                        <p id="wa-qr-hint" class="small text-muted mb-3">Scan QR code dengan WhatsApp di ponsel Anda.</p>
                        <img id="wa-qr-image" src="" alt="QR Code WhatsApp" class="img-fluid" style="max-width: 260px;">
                    </div>
                </div>
            </div>
        @endunless
    </div>
</div>
@endsection

@push('scripts')
@if ($waGatewayConfigured)
@vite(['resources/js/wa-scan.js'])
@endif
@endpush
