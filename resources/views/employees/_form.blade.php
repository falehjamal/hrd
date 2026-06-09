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
    <div class="col-md-6">
        <label class="form-label" for="phone">Telepon</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $employee->phone ?? '') }}" />
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="department">Departemen</label>
        <input type="text" class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department', $employee->department ?? '') }}" />
        @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="position">Jabatan</label>
        <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $employee->position ?? '') }}" />
        @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
