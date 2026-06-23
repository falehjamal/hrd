@extends('layouts.app')

@section('title', 'Edit Jenis Pemotongan')

@section('content')
<x-form-card
    title="Edit Jenis Pemotongan"
    :breadcrumbs="[
        ['label' => 'Jenis Pemotongan', 'url' => route('deduction-types.index')],
        ['label' => 'Edit Jenis Pemotongan'],
    ]"
    back-url="{{ route('deduction-types.index') }}"
>
    <form action="{{ route('deduction-types.update', $deductionType) }}" method="POST">
        @csrf
        @method('PUT')
        @include('deduction-types._form')
        <x-form-actions cancel-url="{{ route('deduction-types.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
