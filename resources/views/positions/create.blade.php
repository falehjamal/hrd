@extends('layouts.app')

@section('title', 'Tambah Jabatan')

@section('content')
<x-form-card
    title="Tambah Jabatan"
    :breadcrumbs="[
        ['label' => 'Data Jabatan', 'url' => route('positions.index')],
        ['label' => 'Tambah Jabatan'],
    ]"
    back-url="{{ route('positions.index') }}"
>
    <form action="{{ route('positions.store') }}" method="POST">
        @csrf
        @include('positions._form')
        <x-form-actions cancel-url="{{ route('positions.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
