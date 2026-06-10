@extends('layouts.app')

@section('title', 'Edit Unit Organisasi')

@section('content')
@include('partials.alerts')

<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Edit Unit Organisasi</h5></div>
    <div class="card-body">
        <form action="{{ route('organizational-units.update', $unit) }}" method="POST">
            @csrf
            @method('PUT')
            @include('organizational-units._form', ['unit' => $unit])
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('organizational-units.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
