@extends('layouts.app')

@section('title', 'Edit Unit Organisasi')

@section('content')
<x-form-card
    title="Edit Unit Organisasi"
    :breadcrumbs="[
        ['label' => 'Organisasi', 'url' => route('organization-structure.index')],
        ['label' => 'Unit Organisasi', 'url' => route('organizational-units.index')],
        ['label' => 'Edit Unit Organisasi'],
    ]"
    back-url="{{ route('organizational-units.index') }}"
>
    <form action="{{ route('organizational-units.update', $unit) }}" method="POST">
        @csrf
        @method('PUT')
        @include('organizational-units._form', ['unit' => $unit])
        <x-form-actions cancel-url="{{ route('organizational-units.index') }}" submit-label="Perbarui" class="mt-4" />
    </form>
</x-form-card>
@endsection
