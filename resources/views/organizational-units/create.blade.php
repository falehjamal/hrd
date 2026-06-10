@extends('layouts.app')

@section('title', 'Tambah Unit Organisasi')

@section('content')
@include('partials.alerts')

<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Tambah Unit Organisasi</h5></div>
    <div class="card-body">
        <form action="{{ route('organizational-units.store') }}" method="POST">
            @csrf
            @include('organizational-units._form', ['unit' => null])
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('organizational-units.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
