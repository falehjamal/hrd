@extends('layouts.app')

@section('title', 'Kuota Cuti — '.$employee->name)

@section('content')
@include('partials.alerts')

<x-page-header title="Kuota Cuti" subtitle="{{ $employee->employee_code }} — {{ $employee->name }} ({{ $year }})">
    <x-slot:actions>
        <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">Kembali</a>
    </x-slot:actions>
</x-page-header>

<div class="card card-modern">
    <div class="card-header"><h5 class="mb-0">Atur Kuota Cuti Tahun {{ $year }}</h5></div>
    <div class="card-body">
        <form action="{{ route('employees.leave-balances.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="year" value="{{ $year }}" />

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Jenis Cuti</th>
                            <th style="width: 140px">Terpakai</th>
                            <th style="width: 180px">Kuota (hari)</th>
                            <th style="width: 120px">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($balances as $index => $balance)
                            <tr>
                                <td>
                                    {{ $balance->leaveType->code }} — {{ $balance->leaveType->name }}
                                    <input type="hidden" name="balances[{{ $index }}][leave_type_id]" value="{{ $balance->leave_type_id }}" />
                                </td>
                                <td>{{ $balance->used_days }} hari</td>
                                <td>
                                    <input type="number" class="form-control @error('balances.'.$index.'.quota_days') is-invalid @enderror"
                                        name="balances[{{ $index }}][quota_days]"
                                        value="{{ old('balances.'.$index.'.quota_days', $balance->quota_days) }}"
                                        min="0" max="365" required />
                                    @error('balances.'.$index.'.quota_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </td>
                                <td>{{ $balance->remaining_days }} hari</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('employees.show', $employee) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
