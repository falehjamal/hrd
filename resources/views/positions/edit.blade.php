@extends('layouts.app')

@section('title', 'Edit Jabatan')

@section('content')
@include('partials.alerts')

<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Edit Jabatan</h5></div>
    <div class="card-body">
        <form action="{{ route('positions.update', $position) }}" method="POST">
            @csrf
            @method('PUT')
            @include('positions._form', ['position' => $position])
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('positions.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
