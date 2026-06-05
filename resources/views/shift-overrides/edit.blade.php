@extends('layouts.app')

@section('title', 'Edit Override Jadwal')

@section('content')
<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Edit Override Jadwal</h5></div>
    <div class="card-body">
        <form action="{{ route('shift-overrides.update', $override) }}" method="POST">
            @csrf
            @method('PUT')
            @include('shift-overrides._form', ['override' => $override])
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('shift-overrides.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
