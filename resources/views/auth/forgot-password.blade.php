@extends('layouts.auth')

@section('title', 'Lupa Password')

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

                    <h4 class="mb-2">Lupa password?</h4>
                    <p class="mb-4">Masukkan email akun Anda. Kami akan mengirim tautan reset password ke email tersebut.</p>

                    @if (session('status'))
                        <div class="alert alert-success mb-3">{{ session('status') }}</div>
                    @endif

                    <form class="mb-3" method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com" required autofocus />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Kirim Tautan Reset</button>
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
