@php
    $isDayOff = old('is_day_off', isset($override) && $override?->shift_id === null && isset($override?->id));
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="employee_id">Karyawan</label>
        <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
            <option value="">-- Pilih --</option>
            @foreach ($employees as $emp)
                <option value="{{ $emp->id }}" @selected(old('employee_id', $override?->employee_id ?? '') == $emp->id)>
                    {{ $emp->employee_code }} — {{ $emp->name }}
                </option>
            @endforeach
        </select>
        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="date">Tanggal</label>
        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $override?->date?->format('Y-m-d') ?? '') }}" required />
        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="is_day_off" name="is_day_off" value="1" {{ $isDayOff ? 'checked' : '' }} />
            <label class="form-check-label" for="is_day_off">Libur (tanpa shift)</label>
        </div>
    </div>
    <div class="col-md-6" id="shift-field">
        <label class="form-label" for="shift_id">Shift</label>
        <select class="form-select @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id">
            <option value="">-- Pilih Shift --</option>
            @foreach ($shifts as $shift)
                <option value="{{ $shift->id }}" @selected(old('shift_id', $override?->shift_id ?? '') == $shift->id)>
                    {{ $shift->code }} - {{ $shift->name }}
                </option>
            @endforeach
        </select>
        @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Catatan</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $override?->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
