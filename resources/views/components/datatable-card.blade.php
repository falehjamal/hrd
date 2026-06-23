@props(['tableId', 'title' => null, 'subtitle' => null])

<div class="card card-modern datatable-card">
    @if ($title || $subtitle || isset($headerActions))
        <div class="card-header datatable-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                @if ($title)
                    <h5 class="datatable-card-title mb-0">{{ $title }}</h5>
                @endif
                @if ($subtitle)
                    <p class="datatable-card-subtitle mb-0">{{ $subtitle }}</p>
                @endif
            </div>
            @if (isset($headerActions))
                <div class="d-flex gap-2 flex-wrap">{{ $headerActions }}</div>
            @endif
        </div>
    @endif
    @if (isset($filters))
        <div class="card-body pb-0">
            <div class="filter-toolbar">{{ $filters }}</div>
        </div>
    @endif
    <div class="card-body {{ isset($filters) ? 'pt-2' : '' }}">
        <div class="table-responsive">
            <table id="{{ $tableId }}" class="table table-modern table-hover w-100">
                {{ $slot }}
            </table>
        </div>
    </div>
</div>
