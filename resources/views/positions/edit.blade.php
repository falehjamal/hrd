@extends('layouts.app')

@section('title', 'Edit Jabatan')

@section('content')
<x-form-card
    title="Edit Jabatan"
    :breadcrumbs="[
        ['label' => 'Data Jabatan', 'url' => route('positions.index')],
        ['label' => 'Edit Jabatan'],
    ]"
    back-url="{{ route('positions.index') }}"
>
    <form action="{{ route('positions.update', $position) }}" method="POST">
        @csrf
        @method('PUT')
        @include('positions._form', ['position' => $position])
        <x-form-actions cancel-url="{{ route('positions.index') }}" submit-label="Perbarui" class="mt-4" />
    </form>
</x-form-card>
@endsection
