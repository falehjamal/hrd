@php
    $checkInTime = old('check_in_time', isset($attendance) && $attendance->check_in_at ? $attendance->check_in_at->format('H:i') : '');
    $checkOutTime = old('check_out_time', isset($attendance) && $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '');
@endphp
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="employee_id">Karyawan</label>
        <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
            <option value="">-- Pilih Karyawan --</option>
            @foreach ($employees as $emp)
                <option value="{{ $emp->id }}" @selected(old('employee_id', $attendance->employee_id ?? '') == $emp->id)>
                    {{ $emp->employee_code }} — {{ $emp->name }}
                </option>
            @endforeach
        </select>
        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="date">Tanggal</label>
        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', isset($attendance) ? $attendance->date->format('Y-m-d') : today()->format('Y-m-d')) }}" required />
        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="check_in_time">Jam Masuk</label>
        <input type="time" class="form-control @error('check_in_time') is-invalid @enderror" id="check_in_time" name="check_in_time" value="{{ $checkInTime }}" />
        @error('check_in_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="check_out_time">Jam Pulang</label>
        <input type="time" class="form-control @error('check_out_time') is-invalid @enderror" id="check_out_time" name="check_out_time" value="{{ $checkOutTime }}" />
        @error('check_out_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Shift Ter-resolve</label>
        <p id="resolved-shift-label" class="form-control-plaintext text-muted mb-0 small">Pilih karyawan dan tanggal</p>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="status">Status</label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $attendance->status ?? \App\Models\Attendance::STATUS_PRESENT) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <x-attendance-camera
            id="check_in_photo"
            name="check_in_photo"
            label="Foto Masuk"
            :required="! isset($attendance)"
        />
        @if(isset($attendance) && $attendance->check_in_photo_path)
            <small class="text-muted d-block mt-1"><a href="{{ route('attendances.photo', [$attendance, 'check-in']) }}" target="_blank">Lihat foto masuk saat ini</a></small>
        @endif
    </div>
    <div class="col-md-6">
        <x-attendance-camera
            id="check_out_photo"
            name="check_out_photo"
            label="Foto Pulang"
            :required="false"
        />
        @if(isset($attendance) && $attendance->check_out_photo_path)
            <small class="text-muted d-block mt-1"><a href="{{ route('attendances.photo', [$attendance, 'check-out']) }}" target="_blank">Lihat foto pulang saat ini</a></small>
        @endif
    </div>
    <div class="col-12">
        <label class="form-label" for="activity_notes">Catatan Aktivitas</label>
        <textarea class="form-control @error('activity_notes') is-invalid @enderror" id="activity_notes" name="activity_notes" rows="3" placeholder="Pekerjaan yang dilakukan karyawan hari ini">{{ old('activity_notes', $attendance->activity_notes ?? '') }}</textarea>
        @error('activity_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="notes">Catatan HR</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" placeholder="Catatan internal / koreksi">{{ old('notes', $attendance->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const employeeSelect = document.getElementById('employee_id');
        const dateInput = document.getElementById('date');
        const labelEl = document.getElementById('resolved-shift-label');
        const url = '{{ route('attendances.resolved-shift') }}';

        const refresh = () => {
            const employeeId = employeeSelect?.value;
            const date = dateInput?.value;
            if (!employeeId || !date || !labelEl) return;
            labelEl.textContent = 'Memuat...';
            fetch(url + '?employee_id=' + encodeURIComponent(employeeId) + '&date=' + encodeURIComponent(date), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then((r) => r.json())
                .then((data) => { labelEl.textContent = data.label || '-'; })
                .catch(() => { labelEl.textContent = 'Gagal memuat shift'; });
        };

        employeeSelect?.addEventListener('change', refresh);
        dateInput?.addEventListener('change', refresh);
        refresh();
    })();
</script>
@endpush
