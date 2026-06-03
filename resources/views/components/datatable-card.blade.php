@props(['tableId', 'title' => null])

<div class="card card-modern">
    @if ($title || isset($headerActions))
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            @if ($title)
                <h5 class="mb-0">{{ $title }}</h5>
            @endif
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
