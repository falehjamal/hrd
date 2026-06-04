<div class="btn-action-group">
    <a href="{{ route('attendances.edit', $attendance) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Edit">
        <i class="bx bx-edit-alt"></i>
    </a>
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('attendances.destroy', $attendance) }}"
        data-delete-message="Hapus absensi {{ $attendance->employee?->name }} tanggal {{ $attendance->date->format('d/m/Y') }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
