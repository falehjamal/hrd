@extends('layouts.app')

@section('title', 'Tambah Shift')

@section('content')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Tambah Shift</h5></div>
    <div class="card-body">
        <form action="{{ route('shifts.store') }}" method="POST">
            @csrf
            @include('shifts._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
