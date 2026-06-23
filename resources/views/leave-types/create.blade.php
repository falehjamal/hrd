@extends('layouts.app')

@section('title', 'Tambah Jenis Cuti')

@section('content')
<x-form-card
    title="Tambah Jenis Cuti"
    :breadcrumbs="[
        ['label' => 'Jenis Cuti', 'url' => route('leave-types.index')],
        ['label' => 'Tambah Jenis Cuti'],
    ]"
    back-url="{{ route('leave-types.index') }}"
>
    <form action="{{ route('leave-types.store') }}" method="POST">
        @csrf
        @include('leave-types._form')
        <x-form-actions cancel-url="{{ route('leave-types.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
