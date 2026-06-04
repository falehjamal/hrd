@extends('layouts.app')

@section('title', 'Tambah Lokasi Kerja')

@section('content')
<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Tambah Lokasi Kerja</h5></div>
    <div class="card-body">
        <form action="{{ route('work-locations.store') }}" method="POST">
            @csrf
            @include('work-locations._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('work-locations.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
