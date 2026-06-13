@extends('layouts.app')

@section('title', 'Edit Pemotongan')

@section('content')
<div class="card card-modern">
    <div class="card-header">
        <h5 class="mb-0">Edit Pemotongan — {{ $employee->name }}</h5>
        <small class="text-muted">{{ $employee->employee_code }}</small>
    </div>
    <div class="card-body">
        <form action="{{ route('deductions.update', $deduction) }}" method="POST">
            @csrf
            @method('PUT')
            @include('employees.deductions._form')
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
