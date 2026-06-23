@extends('layouts.app')

@section('title', 'Tambah Shift')

@section('content')
<x-form-card
    title="Tambah Shift"
    :breadcrumbs="[
        ['label' => 'Data Shift', 'url' => route('shifts.index')],
        ['label' => 'Tambah Shift'],
    ]"
    back-url="{{ route('shifts.index') }}"
>
    <form action="{{ route('shifts.store') }}" method="POST">
        @csrf
        @include('shifts._form')
        <x-form-actions cancel-url="{{ route('shifts.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
