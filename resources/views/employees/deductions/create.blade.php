@extends('layouts.app')

@section('title', 'Tambah Pemotongan')

@section('content')
<x-form-card
    title="Tambah Pemotongan"
    subtitle="{{ $employee->employee_code }} — {{ $employee->name }}"
    :breadcrumbs="[
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
        ['label' => $employee->name, 'url' => route('employees.show', $employee)],
        ['label' => 'Tambah Pemotongan'],
    ]"
    back-url="{{ route('employees.show', $employee) }}"
>
    <form action="{{ route('employees.deductions.store', $employee) }}" method="POST">
        @csrf
        @include('employees.deductions._form')
        <x-form-actions cancel-url="{{ route('employees.show', $employee) }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
