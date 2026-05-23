@extends('layouts.app')

@section('title', 'Edit Shift')

@section('content')
<div class="card">
    <div class="card-header"><h5 class="mb-0">Edit Shift</h5></div>
    <div class="card-body">
        <form action="{{ route('shifts.update', $shift) }}" method="POST">
            @csrf
            @method('PUT')
            @include('shifts._form', ['shift' => $shift])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Perbarui</button>
                <a href="{{ route('shifts.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
