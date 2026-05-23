@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Tambah Karyawan</h5></div>
    <div class="card-body">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            @include('employees._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
