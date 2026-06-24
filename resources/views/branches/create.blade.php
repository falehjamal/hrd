@extends('layouts.app')

@section('title', 'Tambah Cabang')

@section('content')
<x-form-card
    title="Tambah Cabang"
    :breadcrumbs="[
        ['label' => 'Data Cabang', 'url' => route('branches.index')],
        ['label' => 'Tambah Cabang'],
    ]"
    back-url="{{ route('branches.index') }}"
>
    <form action="{{ route('branches.store') }}" method="POST">
        @csrf
        @include('branches._form')
        <x-form-actions cancel-url="{{ route('branches.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
