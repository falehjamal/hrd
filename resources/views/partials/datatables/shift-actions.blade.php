<div class="btn-action-group">
    <x-crud-edit-button modal-id="shiftFormModal" :url="route('shifts.show', $shift)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('shifts.destroy', $shift) }}"
        data-delete-message="Hapus shift {{ $shift->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
