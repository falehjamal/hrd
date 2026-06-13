@extends('layouts.app')

@section('title', 'Tambah Jenis Pemotongan')

@section('content')
@include('partials.alerts')

<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Form Jenis Pemotongan</h5></div>
    <div class="card-body">
        <form action="{{ route('deduction-types.store') }}" method="POST">
            @csrf
            @include('deduction-types._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('deduction-types.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
