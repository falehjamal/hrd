@extends('layouts.app')

@section('title', 'Tambah Absensi')

@section('content')
<x-form-card
    title="Tambah Absensi"
    subtitle="Upload foto absensi"
    :breadcrumbs="[
        ['label' => 'Absensi', 'url' => route('attendances.index')],
        ['label' => 'Tambah Absensi'],
    ]"
    back-url="{{ route('attendances.index') }}"
>
    <form action="{{ route('attendances.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('attendances._form')
        <x-form-actions cancel-url="{{ route('attendances.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
