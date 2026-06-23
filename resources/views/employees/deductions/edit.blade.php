@extends('layouts.app')

@section('title', 'Edit Pemotongan')

@section('content')
<x-form-card
    title="Edit Pemotongan"
    subtitle="{{ $employee->employee_code }} — {{ $employee->name }}"
    :breadcrumbs="[
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
        ['label' => $employee->name, 'url' => route('employees.show', $employee)],
        ['label' => 'Edit Pemotongan'],
    ]"
    back-url="{{ route('employees.show', $employee) }}"
>
    <form action="{{ route('deductions.update', $deduction) }}" method="POST">
        @csrf
        @method('PUT')
        @include('employees.deductions._form')
        <x-form-actions cancel-url="{{ route('employees.show', $employee) }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
