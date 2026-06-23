@extends('layouts.app')

@section('title', 'Tambah Jenis Pemotongan')

@section('content')
<x-form-card
    title="Tambah Jenis Pemotongan"
    :breadcrumbs="[
        ['label' => 'Jenis Pemotongan', 'url' => route('deduction-types.index')],
        ['label' => 'Tambah Jenis Pemotongan'],
    ]"
    back-url="{{ route('deduction-types.index') }}"
>
    <form action="{{ route('deduction-types.store') }}" method="POST">
        @csrf
        @include('deduction-types._form')
        <x-form-actions cancel-url="{{ route('deduction-types.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
