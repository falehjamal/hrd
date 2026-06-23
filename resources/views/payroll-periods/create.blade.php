@extends('layouts.app')

@section('title', 'Buat Periode Gaji')

@section('content')
<x-form-card
    title="Buat Periode Gaji"
    subtitle="Generate draft payroll untuk bulan tertentu"
    :breadcrumbs="[
        ['label' => 'Proses Gaji', 'url' => route('payroll-periods.index')],
        ['label' => 'Buat Periode Gaji'],
    ]"
    back-url="{{ route('payroll-periods.index') }}"
>
    <form action="{{ route('payroll-periods.store') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="period_month">Bulan</label>
                <select class="form-select @error('period_month') is-invalid @enderror" id="period_month" name="period_month" required>
                    @foreach ([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $label)
                        <option value="{{ $num }}" @selected(old('period_month', (int) date('n')) == $num)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('period_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="period_year">Tahun</label>
                <select class="form-select @error('period_year') is-invalid @enderror" id="period_year" name="period_year" required>
                    @foreach ($years as $year)
                        <option value="{{ $year }}" @selected(old('period_year', (int) date('Y')) == $year)>{{ $year }}</option>
                    @endforeach
                </select>
                @error('period_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="notes">Catatan (opsional)</label>
                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="alert alert-info mt-4 mb-0">
            Sistem akan menghitung gaji aktif, pemotongan, cicilan piutang jatuh tempo, dan lembur disetujui untuk semua karyawan aktif.
        </div>
        <x-form-actions cancel-url="{{ route('payroll-periods.index') }}" submit-label="Buat & Generate" class="mt-4" />
    </form>
</x-form-card>
@endsection
