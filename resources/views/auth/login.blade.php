@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body">
                    <div class="app-brand justify-content-center mb-2">
                        <a href="{{ route('login') }}" class="app-brand-link">
                            <span class="app-brand-text demo text-body fw-bolder fs-4">{{ tenant_app_name() }}</span>
                        </a>
                    </div>

                    <h4 class="mb-2">Selamat datang! 👋</h4>
                    <p class="mb-4">Silakan masuk ke akun Anda untuk melanjutkan</p>

                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="login" class="form-label">Email atau Username</label>
                            <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login" value="{{ old('login') }}" placeholder="nama@perusahaan.com atau username" autofocus required />
                            @error('login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember_me" name="remember" />
                                <label class="form-check-label" for="remember_me"> Ingat saya </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
                        </div>
                    </form>

                    <p class="text-center mb-0">
                        <a href="{{ route('platform.login') }}" class="text-muted small">Login admin platform</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
