@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Edit Karyawan</h5></div>
    <div class="card-body">
        <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('employees._form', ['employee' => $employee])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
