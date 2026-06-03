@extends('layouts.app')

@section('title', 'Edit Gaji')

@section('content')
<div class="card card-modern">
    <div class="card-header">
        <h5 class="mb-0">Edit Gaji — {{ $employee->name }}</h5>
        <small class="text-muted">{{ $employee->employee_code }}</small>
    </div>
    <div class="card-body">
        <form action="{{ route('salaries.update', $salary) }}" method="POST">
            @csrf
            @method('PUT')
            @include('employees.salaries._form', ['salary' => $salary])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
