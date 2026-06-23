@extends('layouts.app')

@section('title', 'Edit Override Jadwal')

@section('content')
<x-form-card
    title="Edit Override Jadwal"
    :breadcrumbs="[
        ['label' => 'Jadwal Shift', 'url' => route('shift-overrides.index')],
        ['label' => 'Edit Override Jadwal'],
    ]"
    back-url="{{ route('shift-overrides.index') }}"
>
    <form action="{{ route('shift-overrides.update', $override) }}" method="POST">
        @csrf
        @method('PUT')
        @include('shift-overrides._form', ['override' => $override])
        <x-form-actions cancel-url="{{ route('shift-overrides.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
