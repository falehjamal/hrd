<div class="btn-action-group">
    <x-crud-edit-button modal-id="workLocationFormModal" :url="route('work-locations.show', $loc)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('work-locations.destroy', $loc) }}"
        data-delete-message="Hapus lokasi {{ $loc->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
