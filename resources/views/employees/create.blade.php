@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
<x-form-card
    title="Tambah Karyawan"
    :breadcrumbs="[
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
        ['label' => 'Tambah Karyawan'],
    ]"
    back-url="{{ route('employees.index') }}"
>
    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('employees._form')
        <x-form-actions cancel-url="{{ route('employees.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
