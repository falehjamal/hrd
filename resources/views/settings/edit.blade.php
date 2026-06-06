@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="card card-modern">
    <div class="card-header">
        <h5 class="mb-0">Pengaturan Sistem</h5>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-4" role="tablist">
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
        </ul>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="tab-content">
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
                    <div class="alert alert-info mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        Integrasi gateway WhatsApp akan dihubungkan ke server yang sudah ada. Simpan konfigurasi di bawah agar notifikasi WA siap digunakan.
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="wa_enabled" name="wa_enabled" value="1" @checked(old('wa_enabled', $settings['wa_enabled']) == '1')>
                                <label class="form-check-label" for="wa_enabled">Aktifkan notifikasi WhatsApp</label>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="wa_provider">Provider</label>
                            <input type="text" class="form-control @error('wa_provider') is-invalid @enderror" id="wa_provider" name="wa_provider" value="{{ old('wa_provider', $settings['wa_provider']) }}" placeholder="Contoh: internal">
                            @error('wa_provider')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label" for="wa_base_url">Base URL Gateway</label>
                            <input type="text" class="form-control @error('wa_base_url') is-invalid @enderror" id="wa_base_url" name="wa_base_url" value="{{ old('wa_base_url', $settings['wa_base_url']) }}" placeholder="https://wa.example.com/api">
                            @error('wa_base_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="wa_token">Token / API Key</label>
                            <input type="password" class="form-control @error('wa_token') is-invalid @enderror" id="wa_token" name="wa_token" placeholder="{{ filled($settings['wa_token']) ? '•••••••• (kosongkan jika tidak diubah)' : 'Masukkan token gateway' }}">
                            @error('wa_token')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="wa_sender">Nomor Pengirim</label>
                            <input type="text" class="form-control @error('wa_sender') is-invalid @enderror" id="wa_sender" name="wa_sender" value="{{ old('wa_sender', $settings['wa_sender']) }}" placeholder="62812xxxxxxx">
                            @error('wa_sender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
