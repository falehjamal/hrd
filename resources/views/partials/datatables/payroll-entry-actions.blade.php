<div class="btn-action-group">
    @if (! $entry->is_skipped)
        <a href="{{ route('payroll-periods.entries.show', [$period, $entry]) }}" class="btn btn-sm btn-icon-modern btn-outline-primary" title="Slip Gaji">
            <i class="bx bx-receipt"></i>
        </a>
    @endif
</div>
