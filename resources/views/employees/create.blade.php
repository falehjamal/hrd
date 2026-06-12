@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Tambah Karyawan</h5></div>
    <div class="card-body">
        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('employees._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Simpan</button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
