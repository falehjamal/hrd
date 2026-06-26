<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="effective_date">Tanggal Berlaku</label>
        <input type="date" class="form-control @error('effective_date') is-invalid @enderror" id="effective_date" name="effective_date" value="{{ old('effective_date', isset($salary) ? $salary->effective_date->format('Y-m-d') : date('Y-m-d')) }}" required />
        @error('effective_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="basic_salary">Gaji Pokok (Rp)</label>
        <input type="number" class="form-control @error('basic_salary') is-invalid @enderror" id="basic_salary" name="basic_salary" value="{{ old('basic_salary', isset($salary) ? $salary->basic_salary : '') }}" min="0" step="1" required />
        @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="fixed_allowance">Tunjangan Tetap (Rp)</label>
        <input type="number" class="form-control @error('fixed_allowance') is-invalid @enderror" id="fixed_allowance" name="fixed_allowance" value="{{ old('fixed_allowance', isset($salary) ? $salary->fixed_allowance : 0) }}" min="0" step="1" />
        @error('fixed_allowance')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Catatan</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', isset($salary) ? $salary->notes : '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', isset($salary) ? $salary->is_active : true) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_active">Jadikan gaji aktif (nonaktifkan gaji aktif lain untuk karyawan ini)</label>
        </div>
    </div>
</div>
