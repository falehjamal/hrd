@extends('layouts.app')

@section('title', 'Tambah Gaji')

@section('content')
<x-form-card
    title="Tambah Gaji"
    subtitle="{{ $employee->employee_code }} — {{ $employee->name }}"
    :breadcrumbs="[
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
        ['label' => $employee->name, 'url' => route('employees.show', $employee)],
        ['label' => 'Tambah Gaji'],
    ]"
    back-url="{{ route('employees.show', $employee) }}"
>
    <form action="{{ route('employees.salaries.store', $employee) }}" method="POST">
        @csrf
        @include('employees.salaries._form')
        <x-form-actions cancel-url="{{ route('employees.show', $employee) }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
