<div class="btn-action-group">
    <a href="{{ route('payroll-periods.show', $period) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Detail">
        <i class="bx bx-show"></i>
    </a>
    @if ($period->isDraft())
        <button type="button" class="btn btn-sm btn-icon-modern btn-outline-danger" title="Hapus"
            data-delete-url="{{ route('payroll-periods.destroy', $period) }}"
            data-delete-message="Hapus periode draft {{ $period->periodLabel() }}?">
            <i class="bx bx-trash"></i>
        </button>
    @endif
</div>
