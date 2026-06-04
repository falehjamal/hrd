@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Edit / Koreksi Absensi</h5></div>
    <div class="card-body">
        <form action="{{ route('attendances.update', $attendance) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('attendances._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('attendances.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
