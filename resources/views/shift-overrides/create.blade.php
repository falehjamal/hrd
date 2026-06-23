@extends('layouts.app')

@section('title', 'Tambah Override Jadwal')

@section('content')
<x-form-card
    title="Tambah Override Jadwal"
    :breadcrumbs="[
        ['label' => 'Jadwal Shift', 'url' => route('shift-overrides.index')],
        ['label' => 'Tambah Override Jadwal'],
    ]"
    back-url="{{ route('shift-overrides.index') }}"
>
    <form action="{{ route('shift-overrides.store') }}" method="POST">
        @csrf
        @include('shift-overrides._form', ['override' => $override])
        <x-form-actions cancel-url="{{ route('shift-overrides.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
