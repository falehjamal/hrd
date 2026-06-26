<div class="btn-action-group">
    <x-crud-edit-button modal-id="shiftOverrideFormModal" :url="route('shift-overrides.show', $override)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('shift-overrides.destroy', $override) }}"
        data-delete-message="Hapus override jadwal {{ $override->employee?->name }} tanggal {{ $override->date->format('d/m/Y') }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
