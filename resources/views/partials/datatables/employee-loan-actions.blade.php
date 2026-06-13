<div class="btn-action-group">
    <a href="{{ route('employee-loans.show', $loan) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Detail">
        <i class="bx bx-show"></i>
    </a>
    @if ($loan->status === \App\Models\EmployeeLoan::STATUS_ACTIVE && (float) $loan->paid_amount <= 0)
        <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
            data-delete-url="{{ route('employee-loans.destroy', $loan) }}"
            data-delete-message="Hapus piutang ini?">
            <i class="bx bx-trash"></i>
        </button>
    @endif
</div>
