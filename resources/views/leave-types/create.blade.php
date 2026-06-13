@extends('layouts.app')

@section('title', 'Tambah Jenis Cuti')

@section('content')
@include('partials.alerts')

<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Form Jenis Cuti</h5></div>
    <div class="card-body">
        <form action="{{ route('leave-types.store') }}" method="POST">
            @csrf
            @include('leave-types._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('leave-types.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
