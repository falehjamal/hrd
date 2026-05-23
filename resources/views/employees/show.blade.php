@extends('layouts.app')

@section('title', $employee->name)

@section('content')
@include('partials.alerts')

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Profil Karyawan</h5>
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary">Edit</a>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">{{ $employee->employee_code }}</dd>
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ $employee->name }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $employee->email ?? '-' }}</dd>
                    <dt class="col-sm-4">Telepon</dt>
                    <dd class="col-sm-8">{{ $employee->phone ?? '-' }}</dd>
                    <dt class="col-sm-4">Departemen</dt>
                    <dd class="col-sm-8">{{ $employee->department ?? '-' }}</dd>
                    <dt class="col-sm-4">Jabatan</dt>
                    <dd class="col-sm-8">{{ $employee->position ?? '-' }}</dd>
                    <dt class="col-sm-4">Shift</dt>
                    <dd class="col-sm-8">{{ $employee->shift ? $employee->shift->code.' - '.$employee->shift->name : '-' }}</dd>
                    <dt class="col-sm-4">Bergabung</dt>
                    <dd class="col-sm-8">{{ $employee->join_date?->format('d/m/Y') ?? '-' }}</dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        @if ($employee->status === 'active')
                            <span class="badge bg-label-success">Aktif</span>
                        @else
                            <span class="badge bg-label-secondary">Nonaktif</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Riwayat Gaji</h5>
                <a href="{{ route('employees.salaries.create', $employee) }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i> Tambah Gaji
                </a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Berlaku</th>
                            <th>Gaji Pokok</th>
                            <th>Tunjangan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employee->salaries as $salary)
                            <tr>
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
                                    <form action="{{ route('salaries.destroy', $salary) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data gaji ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">Belum ada data gaji.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Kembali</a>
@endsection
