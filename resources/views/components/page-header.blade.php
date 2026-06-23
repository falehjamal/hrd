@props(['title', 'subtitle' => null, 'breadcrumbs' => []])

<div class="page-header mb-4">
    @if (count($breadcrumbs))
        <nav aria-label="breadcrumb" class="page-breadcrumb mb-2">
            <ol class="breadcrumb mb-0">
                @foreach ($breadcrumbs as $crumb)
                    @if ($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>
    @endif
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div>
            <h4 class="page-header-title">{{ $title }}</h4>
            @if ($subtitle)
                <p class="page-header-subtitle">{{ $subtitle }}</p>
            @endif
        </div>
        @if (isset($actions))
            <div class="d-flex gap-2 flex-wrap align-items-center page-header-actions">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
