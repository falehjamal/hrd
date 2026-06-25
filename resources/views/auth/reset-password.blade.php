@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body">
                    <div class="app-brand justify-content-center mb-3 flex-column">
                        <span class="app-brand-logo"><i class="bx bx-buildings"></i></span>
                        <a href="{{ route('login') }}" class="app-brand-link">
                            <span class="app-brand-text demo text-body fw-bolder fs-5">{{ tenant_app_name() }}</span>
                        </a>
                    </div>

                    <h4 class="mb-2">Reset password</h4>
                    <p class="mb-4">Masukkan password baru untuk akun Anda.</p>

                    <form class="mb-3" method="POST" action="{{ route('password.store') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">Password Baru</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required autocomplete="new-password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('password_confirmation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Reset Password</button>
                        </div>
                    </form>

                    <p class="text-center mb-0">
                        <a href="{{ route('login') }}" class="text-muted small">Kembali ke login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
