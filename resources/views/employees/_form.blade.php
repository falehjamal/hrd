<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="employee_code">ID / NIP Karyawan</label>
        <input type="text" class="form-control @error('employee_code') is-invalid @enderror" id="employee_code" name="employee_code" value="{{ old('employee_code', $employee->employee_code ?? '') }}" required />
        @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="name">Nama Lengkap</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $employee->name ?? '') }}" required />
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->email ?? '') }}" required />
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Dipakai untuk akun login karyawan.</div>
    </div>
    <div class="col-12">
        <hr class="my-1">
        <h6 class="mb-0 text-muted">Akun Login</h6>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="username">Username</label>
        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', isset($employee) ? ($employee->user?->username ?? '') : '') }}" placeholder="Kosongkan = bagian sebelum @ dari email" />
        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Opsional. Default: bagian sebelum @ dari email.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="password">Password</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ isset($employee) && $employee->exists ? 'Kosongkan jika tidak ingin mengubah' : 'Kosongkan = 1234' }}" />
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">
            @if (isset($employee) && $employee->exists)
                Opsional. Kosongkan jika tidak ingin mengubah password.
            @else
                Opsional. Default: 1234.
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" />
    </div>
    <div class="col-12">
        <input type="hidden" name="send_notification" value="0">
        <div class="form-check">
            <input type="checkbox" class="form-check-input @error('send_notification') is-invalid @enderror" id="send_notification" name="send_notification" value="1" @checked(old('send_notification', '1') == '1') />
            <label class="form-check-label" for="send_notification">Kirim notifikasi</label>
            @error('send_notification')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-text">Email ke alamat karyawan, WhatsApp ke nomor telepon (jika dikonfigurasi).</div>
    </div>
    <div class="col-12">
        <input type="hidden" name="has_hr_access" value="0">
        <div class="form-check">
            <input type="checkbox" class="form-check-input @error('has_hr_access') is-invalid @enderror" id="has_hr_access" name="has_hr_access" value="1"
                @checked(old('has_hr_access', isset($employee) ? $employee->user?->isHrUser() : false)) />
            <label class="form-check-label" for="has_hr_access">Akses panel HR</label>
            @error('has_hr_access')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-text">Centang untuk admin/HRD yang tetap memiliki data karyawan.</div>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="phone">Telepon</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" />
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <hr class="my-1">
        <h6 class="mb-0 text-muted">Data Pribadi</h6>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="photo">Foto Profil</label>
        <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" />
        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Opsional. JPG, PNG, atau WebP, maks. 2 MB.</div>
        @if (isset($employee) && $employee->photo_path)
            <div class="mt-2">
                <img src="{{ $employee->photo_url }}" alt="Foto {{ $employee->name }}" class="rounded border" style="width: 96px; height: 96px; object-fit: cover;" id="photo-preview">
            </div>
        @else
            <div class="mt-2 d-none" id="photo-preview-wrap">
                <img src="" alt="Pratinjau foto" class="rounded border" style="width: 96px; height: 96px; object-fit: cover;" id="photo-preview">
            </div>
        @endif
    </div>
    <div class="col-md-4">
        <label class="form-label" for="national_id">NIK</label>
        <input type="text" class="form-control @error('national_id') is-invalid @enderror" id="national_id" name="national_id" value="{{ old('national_id', $employee->national_id ?? '') }}" maxlength="16" inputmode="numeric" />
        @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="gender">Jenis Kelamin</label>
        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
            <option value="">-- Pilih --</option>
            @foreach (\App\Models\Employee::GENDER_LABELS as $value => $label)
                <option value="{{ $value }}" @selected(old('gender', $employee->gender ?? '') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="birth_date">Tanggal Lahir</label>
        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', isset($employee) && $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}" />
        @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-8">
        <label class="form-label" for="address">Alamat</label>
        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $employee->address ?? '') }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="organizational_unit_id">Unit Organisasi</label>
        <select class="form-select @error('organizational_unit_id') is-invalid @enderror" id="organizational_unit_id" name="organizational_unit_id">
            <option value="">-- Pilih Unit --</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('organizational_unit_id', $employee->organizational_unit_id ?? '') == $unit->id)>
                    {{ $unit->name }}
                </option>
            @endforeach
        </select>
        @error('organizational_unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @if ($branches->isNotEmpty())
    <div class="col-md-6">
        <label class="form-label" for="branch_id">Cabang</label>
        <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id">
            <option value="">-- Pilih Cabang --</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $employee->branch_id ?? '') == $branch->id)>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @endif
    <div class="col-md-6">
        <label class="form-label" for="position_id">Jabatan</label>
        <select class="form-select @error('position_id') is-invalid @enderror" id="position_id" name="position_id">
            <option value="">-- Pilih Jabatan --</option>
            @foreach ($positions as $position)
                <option value="{{ $position->id }}" @selected(old('position_id', $employee->position_id ?? '') == $position->id)>
                    {{ $position->name }}
                </option>
            @endforeach
        </select>
        @error('position_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="manager_id">Atasan Langsung</label>
        <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
            <option value="">-- Tanpa atasan --</option>
            @foreach ($managers as $manager)
                <option value="{{ $manager->id }}" @selected(old('manager_id', $employee->manager_id ?? '') == $manager->id)>
                    {{ $manager->employee_code }} — {{ $manager->name }}
                </option>
            @endforeach
        </select>
        @error('manager_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="shift_id">Shift Default (cadangan)</label>
        <select class="form-select @error('shift_id') is-invalid @enderror" id="shift_id" name="shift_id">
            <option value="">-- Pilih Shift --</option>
            @foreach ($shifts as $shift)
                <option value="{{ $shift->id }}" @selected(old('shift_id', $employee->shift_id ?? '') == $shift->id)>
                    {{ $shift->code }} - {{ $shift->name }}
                </option>
            @endforeach
        </select>
        @error('shift_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="join_date">Tanggal Bergabung</label>
        <input type="date" class="form-control @error('join_date') is-invalid @enderror" id="join_date" name="join_date" value="{{ old('join_date', isset($employee) && $employee->join_date ? $employee->join_date->format('Y-m-d') : '') }}" />
        @error('join_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="status">Status</label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            <option value="active" @selected(old('status', $employee->status ?? 'active') === 'active')>Aktif</option>
            <option value="inactive" @selected(old('status', $employee->status ?? '') === 'inactive')>Nonaktif</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

@once
@push('scripts')
<script>
document.getElementById('photo')?.addEventListener('change', function () {
    const file = this.files?.[0];
    const preview = document.getElementById('photo-preview');
    const wrap = document.getElementById('photo-preview-wrap');

    if (!file || !preview) {
        return;
    }

    preview.src = URL.createObjectURL(file);
    wrap?.classList.remove('d-none');
});
</script>
@endpush
@endonce
