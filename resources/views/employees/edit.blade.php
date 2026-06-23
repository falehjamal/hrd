@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
<x-form-card
    title="Edit Karyawan"
    :breadcrumbs="[
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
        ['label' => 'Edit Karyawan'],
    ]"
    back-url="{{ route('employees.index') }}"
>
    <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('employees._form', ['employee' => $employee])
        <x-form-actions cancel-url="{{ route('employees.index') }}" submit-label="Perbarui" class="mt-4" />
    </form>
</x-form-card>
@endsection
