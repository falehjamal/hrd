<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="deduction_type_id">Jenis Pemotongan</label>
        <select class="form-select @error('deduction_type_id') is-invalid @enderror" id="deduction_type_id" name="deduction_type_id" required>
            <option value="">-- Pilih --</option>
            @foreach ($deductionTypes as $type)
                <option value="{{ $type->id }}" @selected(old('deduction_type_id', isset($deduction) ? $deduction->deduction_type_id : '') == $type->id)>{{ $type->code }} — {{ $type->name }}</option>
            @endforeach
        </select>
        @error('deduction_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="effective_date">Tanggal Berlaku</label>
        <input type="date" class="form-control @error('effective_date') is-invalid @enderror" id="effective_date" name="effective_date" value="{{ old('effective_date', isset($deduction) ? $deduction->effective_date->format('Y-m-d') : date('Y-m-d')) }}" required />
        @error('effective_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="amount">Nominal (Rp)</label>
        <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', isset($deduction) ? $deduction->amount : '') }}" min="0" step="1" required />
        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Catatan</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', isset($deduction) ? $deduction->notes : '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', isset($deduction) ? $deduction->is_active : true) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_active">Aktif (nonaktifkan pemotongan aktif lain untuk jenis yang sama)</label>
        </div>
    </div>
</div>
