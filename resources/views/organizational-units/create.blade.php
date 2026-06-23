@extends('layouts.app')

@section('title', 'Tambah Unit Organisasi')

@section('content')
<x-form-card
    title="Tambah Unit Organisasi"
    :breadcrumbs="[
        ['label' => 'Organisasi', 'url' => route('organization-structure.index')],
        ['label' => 'Unit Organisasi', 'url' => route('organizational-units.index')],
        ['label' => 'Tambah Unit Organisasi'],
    ]"
    back-url="{{ route('organizational-units.index') }}"
>
    <form action="{{ route('organizational-units.store') }}" method="POST">
        @csrf
        @include('organizational-units._form', ['unit' => null])
        <x-form-actions cancel-url="{{ route('organizational-units.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
