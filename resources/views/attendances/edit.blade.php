@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
<x-form-card
    title="Edit / Koreksi Absensi"
    :breadcrumbs="[
        ['label' => 'Absensi', 'url' => route('attendances.index')],
        ['label' => 'Edit Absensi'],
    ]"
    back-url="{{ route('attendances.index') }}"
>
    <form action="{{ route('attendances.update', $attendance) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('attendances._form')
        <x-form-actions cancel-url="{{ route('attendances.index') }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
