@extends('layouts.auth')

@section('title', 'Login Platform')

@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card card-modern border-0 shadow-sm">
                <div class="card-body">
                    <div class="app-brand justify-content-center mb-3 flex-column">
                        <span class="app-brand-logo"><i class="bx bx-cloud"></i></span>
                        <a href="{{ route('platform.login') }}" class="app-brand-link">
                            <span class="app-brand-text demo text-body fw-bolder fs-5">{{ config('platform.name') }}</span>
                        </a>
                    </div>

                    <h4 class="mb-2">Admin Platform</h4>
                    <p class="mb-4">Kelola tenant dan monitoring sistem</p>

                    <form class="mb-3" method="POST" action="{{ route('platform.login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus />
                            @error('email')
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
                                <input class="form-check-input" type="checkbox" id="remember" name="remember" />
                                <label class="form-check-label" for="remember"> Ingat saya </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Masuk</button>
                        </div>
                    </form>

                    <p class="text-center mb-0">
                        <a href="{{ route('login') }}" class="text-muted small">Login sebagai pengguna tenant</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
