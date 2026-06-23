@extends('layouts.app')

@section('title', 'Edit Shift')

@section('content')
<x-form-card
    title="Edit Shift"
    :breadcrumbs="[
        ['label' => 'Data Shift', 'url' => route('shifts.index')],
        ['label' => 'Edit Shift'],
    ]"
    back-url="{{ route('shifts.index') }}"
>
    <form action="{{ route('shifts.update', $shift) }}" method="POST">
        @csrf
        @method('PUT')
        @include('shifts._form', ['shift' => $shift])
        <x-form-actions cancel-url="{{ route('shifts.index') }}" submit-label="Perbarui" class="mt-4" />
    </form>
</x-form-card>
@endsection
