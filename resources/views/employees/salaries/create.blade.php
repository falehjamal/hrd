@extends('layouts.app')

@section('title', 'Tambah Gaji')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tambah Gaji — {{ $employee->name }}</h5>
        <small class="text-muted">{{ $employee->employee_code }}</small>
    </div>
    <div class="card-body">
        <form action="{{ route('employees.salaries.store', $employee) }}" method="POST">
            @csrf
            @include('employees.salaries._form')
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
