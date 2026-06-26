<div class="btn-action-group">
    <x-crud-edit-button modal-id="positionFormModal" :url="route('positions.show', $position)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('positions.destroy', $position) }}"
        data-delete-message="Hapus jabatan {{ $position->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
