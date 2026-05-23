@extends('layouts.app')

@section('title', 'Master Gaji')

@section('content')
@include('partials.alerts')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Cari Karyawan</label>
                <input type="text" name="search" class="form-control" placeholder="Nama atau ID karyawan" value="{{ request('search') }}" />
            </div>
            <div class="col-md-3">
                <label class="form-label">Tampilan</label>
                <select name="active_only" class="form-select">
                    <option value="1" @selected(request('active_only', '1') === '1')>Gaji aktif saja</option>
                    <option value="0" @selected(request('active_only') === '0')>Semua riwayat</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                <a href="{{ route('salaries.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Master Gaji Karyawan</h5>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>ID</th>
                    <th>Berlaku</th>
                    <th>Gaji Pokok</th>
                    <th>Tunjangan</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($salaries as $salary)
                    <tr>
                        <td>
                            <a href="{{ route('employees.show', $salary->employee) }}">{{ $salary->employee->name }}</a>
                        </td>
                        <td>{{ $salary->employee->employee_code }}</td>
                        <td>{{ $salary->effective_date->format('d/m/Y') }}</td>
                        <td>{{ format_rupiah($salary->basic_salary) }}</td>
                        <td>{{ format_rupiah($salary->fixed_allowance) }}</td>
                        <td><strong>{{ format_rupiah($salary->total_salary) }}</strong></td>
                        <td>
                            @if ($salary->is_active)
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-secondary">Arsip</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('salaries.edit', $salary) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-edit-alt"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data gaji.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($salaries->hasPages())
        <div class="card-footer">{{ $salaries->links() }}</div>
    @endif
</div>
@endsection
