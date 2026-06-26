<div class="table-responsive">
    <table class="table table-modern mb-0">
        <thead>
            <tr>
                <th>Hari</th>
                <th>Shift</th>
            </tr>
        </thead>
        <tbody>
            @foreach (\App\Models\EmployeeWeeklyShift::DAY_LABELS as $day => $label)
                <tr>
                    <td class="fw-medium">{{ $label }}</td>
                    <td>
                        <select class="form-select @error('shifts.'.$day) is-invalid @enderror" name="shifts[{{ $day }}]">
                            <option value="">Pakai shift default</option>
                            @foreach ($shifts as $shift)
                                <option value="{{ $shift->id }}" @selected(old('shifts.'.$day, $weeklyShifts[$day] ?? '') == $shift->id)>
                                    {{ $shift->code }} - {{ $shift->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('shifts.'.$day)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
