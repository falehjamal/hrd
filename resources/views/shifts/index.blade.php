@extends('layouts.app')

@section('title', 'Data Shift')

@section('content')
@include('partials.alerts')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Shift</h5>
        <a href="{{ route('shifts.create') }}" class="btn btn-primary btn-sm">
            <i class="bx bx-plus me-1"></i> Tambah Shift
        </a>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Jam Kerja</th>
                    <th>Istirahat</th>
                    <th>Karyawan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shifts as $shift)
                    <tr>
                        <td><strong>{{ $shift->code }}</strong></td>
                        <td>{{ $shift->name }}</td>
                        <td>{{ \Illuminate\Support\Str::substr($shift->start_time, 0, 5) }} - {{ \Illuminate\Support\Str::substr($shift->end_time, 0, 5) }}</td>
                        <td>{{ $shift->break_minutes }} menit</td>
                        <td>{{ $shift->employees_count }}</td>
                        <td>
                            @if ($shift->is_active)
                                <span class="badge bg-label-success">Aktif</span>
                            @else
                                <span class="badge bg-label-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-edit-alt"></i></a>
                            <form action="{{ route('shifts.destroy', $shift) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus shift ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data shift.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($shifts->hasPages())
        <div class="card-footer">{{ $shifts->links() }}</div>
    @endif
</div>
@endsection
