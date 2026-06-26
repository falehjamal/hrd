<div class="btn-action-group">
    <x-crud-edit-button modal-id="deductionTypeFormModal" :url="route('deduction-types.show', $type)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('deduction-types.destroy', $type) }}"
        data-delete-message="Hapus jenis pemotongan {{ $type->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
