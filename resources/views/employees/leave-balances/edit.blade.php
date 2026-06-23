@extends('layouts.app')

@section('title', 'Kuota Cuti — '.$employee->name)

@section('content')
<x-form-card
    title="Kuota Cuti"
    subtitle="{{ $employee->employee_code }} — {{ $employee->name }} ({{ $year }})"
    :breadcrumbs="[
        ['label' => 'Data Karyawan', 'url' => route('employees.index')],
        ['label' => $employee->name, 'url' => route('employees.show', $employee)],
        ['label' => 'Kuota Cuti'],
    ]"
    back-url="{{ route('employees.show', $employee) }}"
>
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

        <x-form-actions cancel-url="{{ route('employees.show', $employee) }}" class="mt-4" />
    </form>
</x-form-card>
@endsection
