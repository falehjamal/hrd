<div class="btn-action-group">
    <x-crud-edit-button modal-id="organizationalUnitFormModal" :url="route('organizational-units.show', $unit)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('organizational-units.destroy', $unit) }}"
        data-delete-message="Hapus unit {{ $unit->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
