<div class="btn-action-group">
    <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-icon-modern btn-outline-info" title="Detail" data-bs-toggle="tooltip">
        <i class="bx bx-show"></i>
    </a>
    <x-crud-edit-button modal-id="employeeFormModal" :url="route('employees.show', $employee)" />
    <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
        data-delete-url="{{ route('employees.destroy', $employee) }}"
        data-delete-message="Hapus karyawan {{ $employee->name }}?">
        <i class="bx bx-trash"></i>
    </button>
</div>
