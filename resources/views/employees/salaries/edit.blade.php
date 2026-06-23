@extends('layouts.app')

@section('title', 'Edit Gaji')

@section('content')
<x-form-card
    title="Edit Gaji"
    subtitle="{{ $employee->employee_code }} — {{ $employee->name }}"
    :breadcrumbs="[
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
        ['label' => $employee->name, 'url' => route('employees.show', $employee)],
        ['label' => 'Edit Gaji'],
    ]"
    back-url="{{ route('employees.show', $employee) }}"
>
    <form action="{{ route('salaries.update', $salary) }}" method="POST">
        @csrf
        @method('PUT')
        @include('employees.salaries._form', ['salary' => $salary])
        <x-form-actions cancel-url="{{ route('employees.show', $employee) }}" submit-label="Perbarui" class="mt-4" />
    </form>
</x-form-card>
@endsection
