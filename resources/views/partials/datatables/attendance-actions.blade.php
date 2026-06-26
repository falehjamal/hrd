<div class="btn-action-group">
    <x-crud-edit-button modal-id="attendanceFormModal" :url="route('attendances.show', $attendance)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('attendances.destroy', $attendance) }}"
        data-delete-message="Hapus absensi {{ $attendance->employee?->name }} tanggal {{ $attendance->date->format('d/m/Y') }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
